<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderitemclientRequest;
use App\Http\Requests\OrderitemRequest;
use App\Http\Resources\OrderitemResource;
use App\Models\Order;
use App\Models\Orderitem;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderitemController extends Controller
{
    /**
     * Show all orders in storage by order id.
     */
    public function indexByOrderId(string $id)
    {
        $orderitems = Orderitem::where('order_id', $id)->get();
        return OrderitemResource::collection($orderitems);
    }

    /**
     * Show an order found by id.
     */
    public function show(string $id)
    {
        $orderitem = Orderitem::find($id);

        if($orderitem) {
            return new OrderitemResource($orderitem);

        } else {
            return response()->json([
                'result' => 'No se ha encontrado el pedido.'
            ], 404);
        }
        
    }

    /**
     * Show products by Order Id given by client.
     */
    public function getPedidosItemClient(OrderitemclientRequest $request)
    {
        $user = auth()->user()->id;
        $order = Order::find($request->idPedido);
        $orderitems = Orderitem::where('order_id', $request->idPedido)->get();

        if($orderitems && $order->user_id === $user) { // Comprobamos que el cliente al que pertenece el pedido y el usuario que realizó la solicitud coincidan
            return response()->json([
                'result' => 'Se han encontrado productos.',
                'data' => OrderitemResource::collection($orderitems)
            ], 200);

        } else {
            return response()->json([
                'result' => 'No se han encontrado los productos.'
            ], 404);
        }
    }

    /**
     * Recalculates subtotal and total of the order.
     */
    private function updateTotalOrder(int $pedido_id)
    {
        // Localizamos el pedido completo y el tipo de descuento
        $order = Order::find($pedido_id);
        $tipodescuento = $order->tipo_descuento;

        // Calculamos el subtotal del pedido
        $subtotal = 0;
        foreach($order->orderitems as $orderitem) { 
            $subtotal+= $orderitem->total; // Sumamos los totales de cada item del pedido
        };

        // Calculamos el total del pedido
        if($tipodescuento == 'Porcentual') {
            $order->update([
                'subtotal' => $subtotal,
                'total' => ($subtotal - ($subtotal * ($order->descuento / 100) ))
            ]);

        } else {
            $order->update([
                'subtotal' => $subtotal,
                'total' => ($subtotal - $order->descuento)
            ]);  
        }
    }

    /**
     * Store a newly created order item for a existing order.
     */
    public function store(OrderitemRequest $request)
    {
        $orderitem = new Orderitem;
        $orderitem->order_id = $request->pedido_id;
        $orderitem->product_id = $request->producto_id;
        $orderitem->cantidad = $request->cantidad;
        
        if(!is_null($request->subtotal)) {
            $orderitem->subtotal = $request->subtotal;
        } else {

            $product = Product::find($request->producto_id);
            $orderitem->subtotal = $product->precio_rebajado ?: $product->precio;
        }

        $orderitem->total = ($orderitem->subtotal * $orderitem->cantidad);
        $orderitem->save();

        // Recalcular el subtotal y total del pedido
        $this->updateTotalOrder($request->pedido_id);

        return response()->json([
            'result' => 'Producto añadido al pedido.',
            'data' => $orderitem
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrderitemRequest $request, string $orderitem_id)
    {
        $orderitem = Orderitem::find($orderitem_id);

        if($orderitem) {

            // Comprobamos si el subtotal de la request no es nulo
            if(!is_null($request->subtotal)) {
                
                // Actualizamos directamente el total y el subtotal con el subtotal que nos viene de la request
                $orderitem->update([
                    'product_id' => $request->producto_id,
                    'subtotal' => $request->subtotal,
                    'cantidad' => $request->cantidad,
                    'total' => ($request->subtotal * $request->cantidad)
                ]);

            } else {

                // Localizamos el producto que hemos actualizado en el pedido para luego obtener el precio
                $product = Product::find($request->producto_id);

                if($product) {
                    // Actualizamos el producto del pedido
                    $orderitem->update([
                        'product_id' => $request->producto_id,
                        'cantidad' => $request->cantidad,
                        'subtotal' => $product->precio_rebajado ? $product->precio_rebajado : $product->precio,
                        'total' => ($product->precio_rebajado ? $product->precio_rebajado : $product->precio) * $request->cantidad
                    ]);
                }

            }

            // Recalcular el subtotal y total del pedido
            $this->updateTotalOrder($orderitem->order_id);

            return response()->json([
                'result' => 'Producto del pedido actualizado.',
            ], 200);
            
        }

        return response()->json([
            'result' => 'No se ha encontrado el producto.'
        ], 404);
        
    }

    /**
     * Remove the specified resource from storage and re-calculates subtotal and total.
     */
    public function destroy(string $idArray)
    {
        $ids = explode(",",$idArray); // Obtenemos las id del Array
        $orderitems = Orderitem::find($ids); // Buscamos los productos en la base de datos con las id
        $idPedido = $orderitems->first()->order_id; // Obtenemos el id del pedido

        if($orderitems) {
            $orderitems->map(function($orderitem) {
                $orderitem->delete(); // Eliminamos cada orderitem
            });

            // Recalcular el subtotal y total del pedido
            $this->updateTotalOrder($idPedido);

            return response()->json([
                'result' => 'Producto/s eliminado del pedido.'
            ], 200);

        } else {
            return response()->json([
                'result' => 'No se ha podido procesar la petición.'
            ], 404);
        }
          
    }
}
