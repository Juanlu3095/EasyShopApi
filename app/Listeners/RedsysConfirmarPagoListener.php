<?php

namespace App\Listeners;

use App\Events\RedsysOkEvent;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RedsysConfirmarPagoListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RedsysOkEvent $event): void
    {
        $order = Order::find($event->decode['Ds_Order']);
        $order->update([
            'orderstatus_id' => 4
        ]);
    }
}
