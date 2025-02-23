<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderclientRequest;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Mail\outofstock;
use App\Mail\pedidotransferencia;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Orderitem;
use App\Models\Paymentmethod;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Traits\Emailadmin;

class OrderController extends Controller
{
    use Emailadmin;

    /**
     * Show all orders in storage.
     */
    public function index()
    {
        $orders = Order::all();
        return OrderResource::collection($orders);
    }

    /**
     * Show an order found by id.
     */
    public function show(string $id)
    {
        $order = Order::find($id);

        if($order) {
            return new OrderResource($order);

        } else {
            return response()->json([
                'result' => 'No se ha encontrado el pedido.'
            ], 404);
        }
        
    }

    /**
     * Show order by user id to client.
     */
    public function showToClient(OrderclientRequest $request)
    {
        $order = Order::find($request->idPedido);

        if($order) {
            return new OrderResource($order);

        } else {
            return response()->json([
                'result' => 'No se ha encontrado el pedido.'
            ], 404);
        }
    }

    /**
     * Show orders by user id (customers).
     */
    public function indexByClient()
    {
        $user = auth()->user();

        if($user && $user->role_id === 3) {
            $orders = Order::where('user_id', $user->id)->get();

            return response()->json([
                'result' => 'Pedidos encontrados.',
                'data' => OrderResource::collection($orders)
            ], 200);

        } else {
            return response()->json([
                'result' => 'No tiene pedidos en el sistema.'
            ], 404);
        }
    }

    /**
     * Store a newly created order by client.
     */
    public function store(OrderRequest $request)
    {
        $user = auth('api')->user()->id ?? null; // Si el usuario está registrado se usará su id para asociarle el pedido
        $paymentmethod = Paymentmethod::where('slug', $request->metodo_pago)->first();
        $coupon = Coupon::where('nombre', $request->nombre_descuento)->first();

        // Crear pedido y obtener id para pasarlo a cada item del pedido
        $order = new Order;
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

        $orderid = $order->id;

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

            /* $orderitem = new Orderitem;
            $orderitem->order_id = $orderid;
            $orderitem->product_id = $producto['producto'];
            $orderitem->subtotal = $producto['subtotal'];
            $orderitem->cantidad = $producto['cantidad'];
            $orderitem->total = $producto['total'];
            $orderitem->save(); */

            // Otra forma de hacerlo usando las relaciones y sin necesidad de importar el modelo Orderitem
            $order->orderitems()->create([
                'order_id' => $orderid,
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
            'referencia' => $orderid,
            'nombre_cuenta' => json_decode($paymentmethod->configuracion, true)['nombre'], // true para que devuelva un array
            'banco' => json_decode($paymentmethod->configuracion, true)['nombre_banco'],
            'iban' => json_decode($paymentmethod->configuracion, true)['iban'],
            'bic_swift' => json_decode($paymentmethod->configuracion, true)['bic_swift'],
            'subtotal' => $request->subtotal,
            'metodo_envio' => $order->shippingmethod->nombre,
            'gastos_envio' => $request->gastos_envio,
            'descuento' => $order->descuento ?? 0,
            'tipo_descuento' => $order->tipo_descuento == 'Porcentual' ? '%' : '€',
            'total' => $request->total,
            'productos' => $request->productos
        );

        Mail::to($request->email)->send(new pedidotransferencia($datos));
        
        return response()->json([
            'result' => 'Pedido creado.',
            'data' => $orderid
        ], 201);
    }

    /**
     * Store a newly created order by admin.
     */
    public function storeAdmin(OrderRequest $request)
    {
        $paymentmethod = Paymentmethod::where('slug', $request->metodo_pago)->first();

        // Crear pedido y obtener id para pasarlo a cada item del pedido
        $order = new Order;
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
        $order->subtotal = 0;
        $order->nombre_descuento = $request->nombre_descuento;
        $order->tipo_descuento = $request->tipo_descuento;
        $order->descuento = $request->descuento;
        $order->total = 0;
        $order->user_id = $request->cuenta_cliente;
        $order->shippingmethod_id = $request->metodo_envio;
        $order->gastos_envio = $request->gastos_envio;
        $order->save();

        $orderid = $order->id;
        
        return response()->json([
            'result' => 'Pedido creado.',
            'data' => $orderid
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrderRequest $request, string $order_id)
    {
        $order = Order::find($order_id);

        if($order) {
            $order->update([
                'nombre' => $request->nombre,
                'apellidos' => $request->apellidos,
                'pais' => $request->pais,
                'direccion' => $request->direccion,
                'codigo_postal' => $request->codigo_postal,
                'poblacion' => $request->poblacion,
                'provincia' => $request->provincia,
                'telefono' => $request->telefono,
                'email' => $request->email,
                'notas' => $request->notas,
                'paymentmethod' => Paymentmethod::where('slug', $request->metodo_pago),
                'orderstatus_id' => $request->estado,
                'nombre_descuento' => $request->nombre_descuento,
                'tipo_descuento' => $request->tipo_descuento,
                'descuento' => $request->descuento,
                'user_id' => $request->cuenta_cliente,
                'shippingmethod_id' => $request->metodo_envio,
                'gastos_envio' => $request->gastos_envio
            ]);
    
            // Recalculamos el total del pedido. No el subtotal, por lo que la única cuantía que se puede modificar es el descuento que sólo afecta al total
            if($order->tipo_descuento == 'Porcentual') {
                $order->update([
                    'total' => ($order->subtotal - ($order->subtotal * ($order->descuento / 100) ))
                ]);  
    
            } else {
                $order->update([
                    'total' => ($order->subtotal - $order->descuento)
                ]);  
            }

            return response()->json([
                'result' => 'Pedido actualizado.',
                'data' => $order
            ], 200);

        } else {
            return response()->json([
                'result' => 'Pedido no encontrado.'
            ], 404);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $idArray)
    {
        $ids = explode(",",$idArray); // Obtenemos las id del Array
        $orders = Order::find($ids); // Buscamos los productos en la base de datos con las id

        if($orders) {
            $orders->map(function($order) {
                $order->orderitems()->delete(); // Eliminamos cada orderitem asociado a la order seleccionada

                $order->delete();
            });

            return response()->json([
                'result' => 'Pedido eliminado.',
                'data' => $orders
            ], 200);

        } else {
            return response()->json([
                'result' => 'No se ha podido procesar la petición.'
            ], 404);
        }
          
    }

    /**
     * Sends email with order and payment info to client.
     */
    public function sendEmail(Request $request)
    {
        $order = Order::find($request->id);
        $metodo_pago = Paymentmethod::find($order->paymentmethod_id);

        // Datos del email
        $datos = array(
            'referencia' => $order->id,
            'nombre_cuenta' => json_decode($metodo_pago->configuracion, true)['nombre'], // true para que devuelva un array
            'banco' => json_decode($metodo_pago->configuracion, true)['nombre_banco'],
            'iban' => json_decode($metodo_pago->configuracion, true)['iban'],
            'bic_swift' => json_decode($metodo_pago->configuracion, true)['bic_swift'],
            'subtotal' => $order->subtotal,
            'metodo_envio' => $order->shippingmethod->nombre,
            'gastos_envio' => $order->gastos_envio,
            'descuento' => $order->descuento ?? 0, // Si usamos el operador lógico || va a salir 1 porque lo trata como un bool
            'tipo_descuento' => $order->tipo_descuento == 'Porcentual' ? '%' : '€',
            'total' => $order->total,
            'productos' => $order->orderitems->map(function ($item) { // como hemos convertido una coleccion al usar eloquent en un rray tenemos que mapearlo para seleccionar las propiedades
                return [
                    'nombre_producto' => $item->product->nombre, // 'nombre_producto' es personalizable, pero lo usamos para reutilizar pedidotransferencia.blade.php
                    'subtotal' => $item->subtotal,
                    'cantidad' => $item->cantidad,
                    'total' => $item->total
                ];
            })->toArray(), // Obtenemos los productos de orderitems asociados al pedido
            
        );

        // Envio del email
        Mail::to($order->email)->send(new pedidotransferencia($datos));

        return response()->json([
            'result' => 'Email enviado.',
            'data' => $datos
        ], 200);
    }
}
