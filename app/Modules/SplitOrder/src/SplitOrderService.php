<?php

namespace App\Modules\SplitOrder\src;

use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Warehouse;
use App\Modules\OrderTotals\src\Services\OrderTotalsService;
use App\Services\OrderService;
use Exception;

/**
 *
 */
class SplitOrderService
{
    /**
     * @var Warehouse
     */
    private Warehouse $warehouse;

    /**
     * @var Order|null
     */
    private ?Order $newOrder = null;

    /**
     * @var string|null
     */
    private ?string $newOrderStatus;

    /**
     * @var Order
     */
    private Order $originalOrder;

    public function split(Order $order, Warehouse $warehouse, string $newOrderStatus)
    {
        $this->newOrderStatus = $newOrderStatus;
        $this->warehouse = $warehouse;

        if ($order->lockForEditing()) {
            $this->originalOrder = $order->refresh();
            $this->extractFulfillableProducts();
            $order->unlockFromEditing();
        }
    }

    private function extractFulfillableProducts(): void
    {
        $this->originalOrder->orderProducts
            ->filter(function (OrderProduct $orderProductOriginal) {
                return $orderProductOriginal->quantity_to_ship > 0;
            })
            ->each(function (OrderProduct $orderProduct) {
                /** @var Inventory $inventory */
                $inventory = $orderProduct->product->inventory()
                    ->where(['warehouse_id' => $this->warehouse->getKey()])
                    ->first();

                $quantityToExtract = min($inventory->quantity_available, $orderProduct->quantity_to_ship);

                if ($quantityToExtract <= 0.00) {
                    return true;
                }

                $this->extractOrderProduct($orderProduct, $quantityToExtract, $inventory);
                return true;
            });

        $this->newOrder?->unlockFromEditing();
    }

    private function getNewOrderOrCreate(): Order
    {
        if ($this->newOrder) {
            return $this->newOrder;
        }

        $newOrderNumber = $this->originalOrder->order_number . '-PARTIAL-' . $this->warehouse->code;

        /** @var Order newOrder */
        $this->newOrder = Order::query()
            ->where(['order_number' => $newOrderNumber])
            ->firstOr(function () use ($newOrderNumber) {
                return $this->originalOrder->replicate(['total_paid', 'is_fully_paid', 'total_outstanding', 'total_order']);
            });

        $this->newOrder->custom_unique_reference_id = null;
        $this->newOrder->status_code = $this->newOrderStatus;
        $this->newOrder->is_editing = true;
        $this->newOrder->order_number = $newOrderNumber;

        try {
            $this->newOrder->save();

            activity()->on($this->newOrder)
                ->byAnonymous()
                ->withProperties([
                    'order_number' => $this->originalOrder->order_number,
                    'status_code' => $this->newOrder->status_code
                ])
                ->log('extracted from order');

            activity()->on($this->originalOrder)
                ->byAnonymous()
                ->withProperties(['order_number' => $this->newOrder->order_number])
                ->log('split to order');
        } catch (Exception $exception) {
            report($exception);
            $this->newOrder = Order::whereOrderNumber($newOrderNumber)->first();
        }

        return $this->newOrder;
    }

    private function extractOrderProduct(OrderProduct $orderProduct, int$quantity, Inventory $inventory): void
    {
        $newOrderProduct = $orderProduct->replicate([
            'custom_unique_reference_id',
            'order_id',
            'quantity_ordered',
            'quantity_split',
            'total_price',
            'quantity_to_ship',
            'quantity_to_pick',
            'quantity_picked',
            'quantity_skipped_picking',
            'is_shipped',
        ]);

        $newOrderProduct->order()->associate($this->getNewOrderOrCreate());
        $newOrderProduct->save();

        $recordsUpdatedCount = OrderProduct::query()
            ->whereId($orderProduct->getKey())
            ->whereUpdatedAt($orderProduct->updated_at)
            ->update([
                'quantity_split' => $orderProduct->quantity_split + $quantity,
                'updated_at' => now(),
            ]);

        if ($recordsUpdatedCount === 1) {
            $newOrderProduct->increment('quantity_ordered', $quantity);
            $inventory->increment('quantity_reserved', $quantity);
        }
    }
}
