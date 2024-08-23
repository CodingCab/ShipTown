<?php

namespace App\Modules\Picking\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Models\OrderProduct;
use App\Models\OrderProductPick;
use App\Models\Pick;
use App\Modules\DataCollector\src\Services\DataCollectorService;
use App\Modules\DataCollectorQuantityDiscounts\src\Services\QuantityDiscountsService;
use Illuminate\Support\Facades\Cache;

class DistributePicksJob extends UniqueJob
{
    private ?Pick $pick;

    public function __construct(Pick $pick = null)
    {
        $this->pick = $pick;
    }

    public function handle(): void
    {
        $key = implode('_', [self::class, data_get($this->pick, 'id', 0)]);

        Cache::lock($key, 30)->get(function () {
            if ($this->pick) {
                $this->distributePick($this->pick);
                return;
            }

            Pick::query()
                ->where('is_distributed', false)
                ->each(function (Pick $pick) {
                    $this->distributePick($pick);
                });
        });
    }

    public function distributePick(Pick $pick): void
    {
        $orderProducts = OrderProduct::query()
            ->whereIn('id', $pick->order_product_ids)
            ->oldest('order_id')
            ->get();

        if ($pick->quantity_picked != 0) {
            $key = 'quantity_picked';
            $quantityToDistribute = $pick->quantity_picked;
        } else {
            $key = 'quantity_skipped_picking';
            $quantityToDistribute = $pick->quantity_skipped_picking;
        }

        foreach ($orderProducts as $orderProduct) {
            $quantity = min($quantityToDistribute, $orderProduct->quantity_to_pick);
            $orderProduct->fill([
                $key => $orderProduct->getAttribute($key) + $quantity,
            ]);
            $orderProduct->save();

            OrderProductPick::query()->create([
                'pick_id' => $pick->id,
                'order_product_id' => $orderProduct->id,
                $key => $quantity,
            ]);

            $quantityToDistribute -= $quantity;
            if ($quantityToDistribute <= 0) {
                $pick->update(['is_distributed' => true]);
                break;
            }
        }
    }
}
