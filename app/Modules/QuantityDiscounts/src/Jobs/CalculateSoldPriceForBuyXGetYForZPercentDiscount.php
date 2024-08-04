<?php

namespace App\Modules\QuantityDiscounts\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Models\DataCollection;
use App\Models\DataCollectionRecord;
use App\Modules\DataCollector\src\Services\DataCollectorService;
use App\Modules\QuantityDiscounts\src\Models\QuantityDiscount;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CalculateSoldPriceForBuyXGetYForZPercentDiscount extends UniqueJob
{
    private QuantityDiscount $discount;
    private DataCollection $dataCollection;

    public function uniqueId(): string
    {
        return implode('_', [self::class, $this->dataCollection->id]);
    }

    public function __construct(DataCollection $dataCollection, QuantityDiscount $discount)
    {
        $this->discount = $discount;
        $this->dataCollection = $dataCollection;
    }

    public function handle(): void
    {
        $cacheLockKey = implode('-', ['recalculating_quantity_discounts_for_data_collection', $this->dataCollection->id]);

        Cache::lock($cacheLockKey, 5)->get(function () {
            $this->preselectEligibleRecords()
                ->applyDiscountsToSelectedRecords();

            DataCollectorService::recalculate();
        });
    }

    private function recordsEligibleForDiscount(): Builder
    {
        return $this->dataCollection->records()
            ->getQuery()
            ->whereIn('product_id', Arr::pluck($this->discount->products, 'product_id'))
            ->where(function ($query) {
                $query->whereNull('price_source_id')
                    ->orWhere(['price_source_id' => $this->discount->id]);
            })
            ->orderBy('unit_full_price', 'ASC')
            ->orderBy('price_source', 'DESC')
            ->orderBy('quantity_scanned', 'DESC')
            ->orderBy('id', 'ASC');
    }

    public function preselectEligibleRecords(): self
    {
        $eligibleRecords = $this->recordsEligibleForDiscount()->get();
        $remainingQuantityToDistribute = $this->discount->total_quantity_per_discount * $this->timesWeCanApplyOfferFor($eligibleRecords);

        $eligibleRecords->each(function (DataCollectionRecord $record) use (&$remainingQuantityToDistribute) {
            if ($remainingQuantityToDistribute <= 0 && $record->price_source_id === $this->discount->id) {
                $record->update([
                    'unit_sold_price' => $record->unit_full_price,
                    'price_source' => null,
                    'price_source_id' => null,
                ]);
            }

            if ($remainingQuantityToDistribute <= 0) {
                return true;
            }

            if ($remainingQuantityToDistribute >= $record->quantity_scanned) {
                $record->update([
                    'price_source' => 'QUANTITY_DISCOUNT',
                    'price_source_id' => $this->discount->id,
                ]);

                $remainingQuantityToDistribute -= $record->quantity_scanned;
            } else {
                $record->update([
                    'quantity_scanned' => $record->quantity_scanned - $remainingQuantityToDistribute,
                    'price_source' => null,
                    'price_source_id' => null,
                ]);

                $record->replicate()
                    ->fill([
                        'quantity_scanned' => $remainingQuantityToDistribute,
                        'price_source' => 'QUANTITY_DISCOUNT',
                        'price_source_id' => $this->discount->id,
                    ])
                    ->save();

                $remainingQuantityToDistribute = 0;
            }

            return true;
        });

        return $this;
    }

    public function applyDiscountsToSelectedRecords(): self
    {
        $eligibleRecords = $this->recordsEligibleForDiscount()
            ->where(['price_source_id' => $this->discount->id])
            ->get();

        $quantityToDistribute = $this->discount->quantity_at_discounted_price * $this->timesWeCanApplyOfferFor($eligibleRecords);

        $eligibleRecords->each(function (DataCollectionRecord $record) use (&$quantityToDistribute) {
            $discountedPrice = $record->unit_full_price * ($this->discount->configuration['discount_percent'] / 100);

            if ($quantityToDistribute <= 0 && $record->unit_sold_price != $record->unit_full_price) {
                $record->update(['unit_sold_price' => $record->unit_full_price]);
            }

            if ($quantityToDistribute <= 0) {
                return true;
            }

            if ($quantityToDistribute >= $record->quantity_scanned) {
                $record->update(['unit_sold_price' => $discountedPrice]);
                $quantityToDistribute -= $record->quantity_scanned;
            } else {
                $record->update([
                    'quantity_scanned' => $record->quantity_scanned - $quantityToDistribute,
                    'unit_sold_price' => $record->unit_full_price
                ]);

                $record->replicate()
                    ->fill([
                        'quantity_scanned' => $quantityToDistribute,
                        'unit_sold_price' => $discountedPrice
                    ])
                    ->save();

                $quantityToDistribute = 0;
            }

            return true;
        });

        return $this;
    }

    private function timesWeCanApplyOfferFor(Collection $records): int
    {
        $totalQuantityPerDiscount = $this->discount->quantity_at_full_price + $this->discount->quantity_at_discounted_price;

        return floor($records->sum('quantity_scanned') / ($totalQuantityPerDiscount));
    }
}
