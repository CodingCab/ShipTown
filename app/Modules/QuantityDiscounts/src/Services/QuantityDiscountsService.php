<?php

namespace App\Modules\QuantityDiscounts\src\Services;

use App\Models\DataCollection;
use App\Models\DataCollectionRecord;
use App\Modules\QuantityDiscounts\src\Models\QuantityDiscount;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class QuantityDiscountsService
{
    public static function preselectEligibleRecords(DataCollection $dataCollection, QuantityDiscount $discount): void
    {
        $eligibleRecords = self::getRecordsEligibleForDiscount($dataCollection, $discount)->get();
        $remainingQuantityToDistribute = $discount->total_quantity_per_discount * self::timesWeCanApplyOfferFor($eligibleRecords, $discount);

        $eligibleRecords->each(function (DataCollectionRecord $record) use (&$remainingQuantityToDistribute, $discount) {
            if ($remainingQuantityToDistribute <= 0 && $record->price_source_id === $discount->id) {
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
                    'price_source_id' => $discount->id,
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
                        'price_source_id' => $discount->id,
                    ])
                    ->save();

                $remainingQuantityToDistribute = 0;
            }

            return true;
        });
    }

    public static function getRecordsEligibleForDiscount(DataCollection $dataCollection, QuantityDiscount $discount): Builder
    {
        return $dataCollection->records()
            ->getQuery()
            ->whereIn('product_id', Arr::pluck($discount->products, 'product_id'))
            ->where(function ($query) use ($discount) {
                $query->whereNull('price_source_id')
                    ->orWhere(['price_source_id' => $discount->id]);
            })
            ->orderBy('unit_full_price', 'ASC')
            ->orderBy('price_source', 'DESC')
            ->orderBy('quantity_scanned', 'DESC')
            ->orderBy('id', 'ASC');
    }

    public static function timesWeCanApplyOfferFor(Collection $records, QuantityDiscount $discount): int
    {
        $totalQuantityPerDiscount = $discount->quantity_at_full_price + $discount->quantity_at_discounted_price;

        return floor($records->sum('quantity_scanned') / ($totalQuantityPerDiscount));
    }
}
