<?php

namespace App\Modules\AutoTags\src\Listeners\OrderCreatedEvent;

use App\Events\Order\OrderCreatedEvent;
use App\Services\OrderService;

class ToggleOrderOutOfStockTagListener
{
    /**
     * Handle the event.
     *
     * @param OrderCreatedEvent $event
     *
     * @return void
     */
    public function handle(OrderCreatedEvent $event): void
    {
        $order = $event->getOrder();

        if (OrderService::canNotFulfill($order)) {
            $order->attachTag('Out Of Stock');
        } else {
            $order->detachTag('Out Of Stock');
        }
    }
}
