<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Orderitem;
use App\Models\Paymentmethod;
use Illuminate\Http\Request;

class OrderController extends Controller
{
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
     * Store a newly created order by client.
     */
    public function store(OrderRequest $request)
    {
        $user = auth('api')->user()->id ?? null; // Si el usuario está registrado se usará su id para asociarle el pedido
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
        $order->subtotal = $request->subtotal;
        $order->nombre_descuento = $request->nombre_descuento;
        $order->tipo_descuento = $request->tipo_descuento;
        $order->descuento = $request->descuento;
        $order->total = $request->total;
        $order->user_id = $user;
        $order->save();

        $orderid = $order->id;

        // Creamos los items del pedido
        // Usamos foreach porque map no nos permite incluir dentro la variable $orderid si no usamos use
        foreach ($request->productos as $producto) {
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
                'total' => $producto['total']
            ]);
        }
        
        return response()->json([
            'result' => 'Pedido creado.',
            'data' => $order
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
                'user_id' => $request->cuenta_cliente
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
}
