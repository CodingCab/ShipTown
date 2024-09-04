<?php

namespace App\Modules\AutoTags\src\Listeners\OrderUpdatedEvent;

use App\Events\Order\OrderUpdatedEvent;
use App\Services\OrderService;

class ToggleOrderOutOfStockTagListener
{
    /**
     * Handle the event.
     *
     *
     * @return void
     */
    public function handle(OrderUpdatedEvent $event)
    {
        $order = $event->getOrder();

        if (OrderService::canNotFulfill($order)) {
            $order->attachTag('Out Of Stock');
        } else {
            $order->detachTag('Out Of Stock');
        }
    }
}
