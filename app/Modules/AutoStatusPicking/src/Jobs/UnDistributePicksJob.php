<?php

namespace App\Modules\AutoStatusPicking\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Models\OrderProduct;
use App\Models\OrderProductPick;
use App\Models\Pick;
use Illuminate\Support\Facades\Cache;

class UnDistributePicksJob extends UniqueJob
{
    private ?Pick $pick;
    private string $key;

    public function __construct(Pick $pick = null)
    {
        $this->pick = $pick;
        $this->key = implode('_', [get_class($this), data_get($this->pick, 'id', 0)]);
    }

    public function uniqueId(): string
    {
        return $this->key;
    }

    public function handle(): void
    {
        Cache::lock($this->key, 30)->get(function () {
            Pick::query()
                ->onlyTrashed()
                ->where('is_distributed', true)
                ->whereNot('quantity_distributed', 0)
                ->when($this->pick, function ($query) {
                    $query->where('id', $this->pick->id);
                })
                ->each(function (Pick $pick) {
                    $this->unDistributePick($pick);
                });
        });
    }

    public function unDistributePick(Pick $pick): void
    {
        OrderProductPick::query()
            ->where('pick_id', $pick->id)
            ->each(function (OrderProductPick $orderProductPick) {
                $orderProduct = OrderProduct::query()->find($orderProductPick->order_product_id);

                if ($this->pick->quantity_picked != 0) {
                    $key = 'quantity_picked';
                } else {
                    $key = 'quantity_skipped_picking';
                }

                $orderProduct->fill([
                    $key => max(0, $orderProduct->getAttribute($key) - $orderProductPick->getAttribute($key)),
                ]);
                $orderProduct->save();
                $orderProductPick->decrement($key, $orderProductPick->getAttribute($key));

                $this->pick->decrement('quantity_distributed', $orderProductPick->getAttribute($key));
            });
    }
}
