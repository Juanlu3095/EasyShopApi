<?php

namespace App\Http\Controllers;

use App\Events\RedsysOkEvent;
use App\Http\Requests\OrderRequest;
use Exception;
use Illuminate\Http\Request;
use Ssheduardo\Redsys\Facades\Redsys;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Paymentmethod;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\Mail;
use App\Mail\outofstock;
use App\Mail\pedidotarjeta;

class RedsysController extends Controller
{
    public function pagoTarjeta (OrderRequest $request)
    {
        $user = auth('api')->user()->id ?? null; // Si el usuario está registrado se usará su id para asociarle el pedido
        $paymentmethod = Paymentmethod::where('slug', $request->metodo_pago)->first();
        $coupon = Coupon::where('nombre', $request->nombre_descuento)->first();

        // Crear pedido y obtener id para pasarlo a cada item del pedido
        $order = new Order;
        $order->id = time();
        $order->nombre = $request->nombre;
        $order->apellidos = $request->apellidos;
        $order->pais = $request->pais;
        $order->direccion = $request->direccion;
        $order->codigo_postal = $request->codigo_postal;
        $order->poblacion = $request->poblacion;
        $order->provincia = $request->provincia;
        $order->telefono = $request->telefono;
        $order->email = $request->email;
        $order->notas = $request->notas;
        $order->paymentmethod_id = $paymentmethod->id;
        $order->subtotal = $request->subtotal;
        $order->nombre_descuento = $request->nombre_descuento;
        $order->tipo_descuento = $request->tipo_descuento;
        $order->descuento = $request->descuento;
        $order->total = $request->total;
        $order->user_id = $user;
        $order->shippingmethod_id = $request->metodo_envio;
        $order->gastos_envio = $request->gastos_envio;
        $order->save();

        // Si hay descuento y aún tiene usos, descontamos del limite de usos
        if($coupon && $coupon->limite_uso > 0) {
            $coupon->limite_uso = $coupon->limite_uso - 1;
            $coupon->save();
        }

        // Creamos los items del pedido
        // Usamos foreach porque map no nos permite incluir dentro la variable $orderid si no usamos use
        foreach ($request->productos as $producto) {

            // Descontamos los productos del inventario si lo hemos habilitado. Dejamos que compre el producto aunque no haya existencias.
            $product = Product::find($producto['producto']);
            if($product->inventario) {
                if($product->inventario <= $producto['cantidad']) {
                    $email = $this->adminEmail(); // Obtenemos el email de administración

                    $data = array(
                        'producto' => $product->nombre 
                    );

                    Mail::to($email)->send(new outofstock($data)); // Mandamos el email a admin si el producto se va a quedar sin existencias
                }

                // Actualizamos el inventario
                $product->update([
                    'inventario' => $product->inventario - $producto['cantidad']
                ]);
            }

            // Guardamos los items de la compra
            $order->orderitems()->create([
                'order_id' => $order->id,
                'product_id' => $producto['producto'],
                'subtotal' => $producto['subtotal'],
                'cantidad' => $producto['cantidad'],
                'total' => $producto['total'],
            ]);

            // Añadimos a ventas para las estadísticas
            $sale = Sale::where('product_id', $producto['producto'])->first();

            // Si el producto está en la tabla de ventas, actualiza beneficios y número de ventas
            if($sale) {
                $sale->update([
                    'ventas' => $sale->ventas + $producto['cantidad'],
                    'beneficios' => $sale->beneficios + $producto['total']
                ]);

            } else {

                Sale::create([
                    'product_id' => $producto['producto'],
                    'ventas' => $producto['cantidad'],
                    'beneficios' => $producto['total'],
                ]);
            }

        }

        // Envio del email al cliente con los datos del pedido
        $datos = array(
            'referencia' => $order->id,
            'subtotal' => $request->subtotal,
            'metodo_envio' => $order->shippingmethod->nombre,
            'gastos_envio' => $request->gastos_envio,
            'descuento' => $order->descuento ?? 0,
            'tipo_descuento' => $order->tipo_descuento == 'Porcentual' ? '%' : '€',
            'total' => $request->total,
            'productos' => $request->productos
        );

        Mail::to($request->email)->send(new pedidotarjeta($datos));

        try {
            $key = config('redsys.key');
            $code = config('redsys.merchantcode');

            Redsys::setAmount($order->total);
            Redsys::setOrder($order->id); // Redsys sólo permite que el número de pedido tenga 12 dígitos
            Redsys::setMerchantcode($code); //Reemplazar por el código que proporciona el banco
            Redsys::setCurrency('978');
            Redsys::setTransactiontype('0');
            Redsys::setTerminal('1');
            Redsys::setMethod('T'); //Solo pago con tarjeta, no mostramos iupay
            Redsys::setNotification(config('redsys.url_notification')); //Url de notificacion
            Redsys::setUrlOk(config('redsys.url_ok')); //Url OK
            Redsys::setUrlKo(config('redsys.url_ko')); //Url KO
            Redsys::setVersion('HMAC_SHA256_V1');
            Redsys::setTradeName(config('redsys.tradename'));
            Redsys::setTitular(config('redsys.titutar'));
            Redsys::setEnviroment(config('redsys.enviroment')); //Entorno test o live

            $signature = Redsys::generateMerchantSignature($key); // Genera Ds_Signature
            Redsys::setMerchantSignature($signature);

            return response()->json([
                'form' => Redsys::createForm() // devuelve el formulario al frontend
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'result' => 'Ha ocurrido un error.'
            ], 500);
        }
    }

    // Esta función se ejecutará si el pago de Redsys es correcto. Sólo para la redirección al front, sin $request
    public function ok(Request $request)
    {
        $message = $request->all();
        $key = config('redsys.key');

        if(isset($message['Ds_MerchantParameters'])) {
            $decode = json_decode(base64_decode($message['Ds_MerchantParameters']), true);
            
            if(Redsys::check($key, $request->input()) && $decode['Ds_Response'] <= 99) { // Verifica Ds_Signature y si el pago se ha realizado
                RedsysOkEvent::dispatch($decode); // Editamos el estado del pedido y lo ponemos como pagado/confirmado
                return redirect()->to(env('APP_FRONT_URL'). '/resultado-del-pago/ok');
            }
        }
        return redirect()->to(env('APP_FRONT_URL'). '/resultado-del-pago/error');
    }

    // Esta función se ejecutará si el pago de Redsys no es correcto. Sólo para la redirección al front
    public function ko()
    {
        return redirect()->to(env('APP_FRONT_URL'). '/resultado-del-pago/error');
    }

    // Esta función se ejecutará para notificar a Laravel de que el pago ha sido realizado, y por tanto, actualizar el estado del pedido. PROBAR EN PRODUCCIÓN
    public function notification(Request $request)
    {
        /* $message = $request->all();
        $key = config('redsys.key');

        if(isset($message['Ds_MerchantParameters'])) {
            $decode = json_decode(base64_decode($message['Ds_MerchantParameters']), true);
            $date = urldecode($decode['Ds_Date']);
            $hour = urldecode($decode['Ds_Hour']);
            $decode['Ds_Date'] = $date;
            $decode['Ds_Hour'] = $hour;
            
            if(Redsys::check($key, $request->input()) && $decode['Ds_Response'] <= 99) { // Verifica Ds_Signature y si el pago se ha realizado
                RedsysOkEvent::dispatch($decode); // Editamos el estado del pedido y lo ponemos como pagado/confirmado
                return response()->json(['Success' => true, 'message' => $message, 'decode' => $decode ]); // FALTA LA REDIRECCION AL FRONT Y MOSTRAR TODO OK
            }
        }
        return response()->json(data: ['Success' => false, 'message' => $request->all()]); */
    }

}
