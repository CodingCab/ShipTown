<?php

namespace App\Modules\QuantityDiscounts\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Models\DataCollection;
use App\Models\DataCollectionRecord;
use App\Modules\DataCollector\src\Services\DataCollectorService;
use App\Modules\QuantityDiscounts\src\Models\QuantityDiscount;
use App\Modules\QuantityDiscounts\src\Services\QuantityDiscountsService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CalculateSoldPriceForBuyXGetYForZPriceDiscount extends UniqueJob
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
            QuantityDiscountsService::preselectEligibleRecords($this->dataCollection, $this->discount);
            $this->applyDiscountsToSelectedRecords();
            DataCollectorService::recalculate($this->dataCollection);
        });
    }

    public function applyDiscountsToSelectedRecords(): self
    {
        $eligibleRecords = QuantityDiscountsService::getRecordsEligibleForDiscount($this->dataCollection, $this->discount)
            ->where(['price_source_id' => $this->discount->id])
            ->get();

        $quantityToDistribute = $this->discount->quantity_at_discounted_price * $this->timesWeCanApplyOfferFor($eligibleRecords);

        $eligibleRecords->each(function (DataCollectionRecord $record) use (&$quantityToDistribute) {
            if ($quantityToDistribute <= 0 && $record->unit_sold_price != $record->unit_full_price) {
                $record->update(['unit_sold_price' => $record->unit_full_price]);
            }

            if ($quantityToDistribute <= 0) {
                return true;
            }

            if ($quantityToDistribute >= $record->quantity_scanned) {
                $record->update(['unit_sold_price' => $this->discount->configuration['discounted_price']]);
                $quantityToDistribute -= $record->quantity_scanned;
            } else {
                $record->update([
                    'quantity_scanned' => $record->quantity_scanned - $quantityToDistribute,
                    'unit_sold_price' => $record->unit_full_price
                ]);

                $record->replicate()
                    ->fill([
                        'quantity_scanned' => $quantityToDistribute,
                        'unit_sold_price' => $this->discount->configuration['discounted_price']
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
