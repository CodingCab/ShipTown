<?php

namespace App\Modules\QuantityDiscounts\src\Jobs;

use App\Models\DataCollectionRecord;
use App\Modules\QuantityDiscounts\src\Models\QuantityDiscount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class CalculateSoldPriceForBuyXGetYForZPercentDiscount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private QuantityDiscount $discount;
    private Collection $filteredCollectionRecords;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(QuantityDiscount $discount, Collection $filteredCollectionRecords)
    {
        $this->discount = $discount;
        $this->filteredCollectionRecords = $filteredCollectionRecords;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $discountConfig = $this->discount->configuration;

        $requiredQuantity = (int)data_get($discountConfig, 'quantity_full_price', 0);
        $discountedQuantity = (int)data_get($discountConfig, 'quantity_discounted', 0);

        $totalQuantityRequired = $requiredQuantity + $discountedQuantity;

        $discountPercent = (int)data_get($discountConfig, 'discount_percent', 0);

        if ($requiredQuantity === 0 || $discountedQuantity === 0 || $discountPercent === 0) {
            return;
        }

        if ($this->filteredCollectionRecords->count() > 1) {
            $lowestPriceRecord = $this->filteredCollectionRecords
                ->whereNull('price_source_id')
                ->sortBy('unit_full_price')
                ->first();
            $referenceRecord = $this->filteredCollectionRecords
                ->whereNull('price_source_id')
                ->sortBy('unit_full_price')
                ->last();
            $alreadyDiscountedRecords = $this->filteredCollectionRecords
                ->whereNotNull('price_source_id')
                ->all();
            $totalDiscounted = collect($alreadyDiscountedRecords)->sum('quantity_scanned');
            $lowestPriceAlreadyDiscounted = collect($alreadyDiscountedRecords)
                ->where('product_id', $lowestPriceRecord->product_id)
                ->first();
        } else {
            $lowestPriceRecord = $referenceRecord = $this->filteredCollectionRecords->first();
            $totalDiscounted = 0;
            $lowestPriceAlreadyDiscounted = null;
        }

        if ($lowestPriceRecord === $referenceRecord) {
            $totalQuantity = $referenceRecord->quantity_scanned + $totalDiscounted;
        } else {
            $totalQuantity = $referenceRecord->quantity_scanned + $lowestPriceRecord->quantity_scanned + $totalDiscounted;
        }

        if ($totalQuantity % ($requiredQuantity + $discountedQuantity) !== 0) {
            return;
        }

        $newSoldPrice = $lowestPriceRecord->unit_full_price - ($lowestPriceRecord->unit_full_price * $discountPercent / 100);

        if ($lowestPriceRecord->unit_sold_price > $newSoldPrice) {
            if ($lowestPriceRecord->quantity_scanned > 1) {
                $discounted = min($lowestPriceRecord->quantity_scanned, $discountedQuantity);


//
//
//                $promotionTimesApplied = $totalDiscounted % $totalQuantityRequired;
//
//                $recordWithoutPromotion = $lowestPriceRecord;
//                $recordWithoutPromotion->quantity_scanned = $totalDiscounted - $totalQuantityRequired;
//
//                $recordWithPromotionFullPrice = DataCollectionRecord::firstOrCreate([
//                    'product_id' = $lowestPriceRecord->product_id,
//                    'price_source' => 'QUANTITY_DISCOUNT',
//                    'price_source_id' => $this->discount->id
//                ], [
//                    'quantity_scanned' => 0,
//                ]);
//
//                $recordWithPromotionDiscountedPrice = DataCollectionRecord::firstOrCreate([
//                    'product_id' = $lowestPriceRecord->product_id,
//                    'price_source' => 'QUANTITY_DISCOUNT',
//                    'price_source_id' => $this->discount->id
//                ], [
//                    'quantity_scanned' => 0,
//                ]);
//
//                $totalQuantityScanned = $totalDiscounted;
//
//
//                0 $recordWithoutPromotion->quantity_scanned = $totalQuantityScanned - $totalQuantityRequired * $promotionTimesApplied;
//                5 $recordWithPromotionFullPrice->quantity_scanned = $requiredQuantity * $promotionTimesApplied;
//                5 $recordWithPromotionDiscountedPrice->quantity_scanned = $discountedQuantity * $promotionTimesApplied;
//
//                0 $recordWithoutPromotion->quantity_scanned = $totalQuantityScanned - $totalQuantityRequired * $promotionTimesApplied;
//                0 $recordWithPromotionFullPrice->quantity_scanned = $requiredQuantity * $promotionTimesApplied;
//                10 $recordWithPromotionDiscountedPrice->quantity_scanned = $discountedQuantity * $promotionTimesApplied;
//
//                0 $recordWithoutPromotion->quantity_scanned = $totalQuantityScanned - $totalQuantityRequired * $promotionTimesApplied;
//                5 $recordWithPromotionFullPrice->quantity_scanned = $requiredQuantity * $promotionTimesApplied;
//                5 $recordWithPromotionDiscountedPrice->quantity_scanned = $discountedQuantity * $promotionTimesApplied;
//


                if ($lowestPriceAlreadyDiscounted) {
                    $lowestPriceAlreadyDiscounted->updateQuietly([
                        'quantity_scanned' => $lowestPriceAlreadyDiscounted->quantity_scanned + $discounted,
                    ]);
                } else {
                    $newRecord = $lowestPriceRecord
                        ->replicateQuietly([
                            'quantity_to_scan',
                            'unit_discount',
                            'total_discount',
                            'total_price',
                            'total_cost',
                            'total_sold_price',
                            'total_profit',
                            'is_requested',
                            'is_fully_scanned',
                            'is_over_scanned'
                        ])
                        ->fill([
                            'unit_sold_price' => $newSoldPrice,
                            'price_source' => 'QUANTITY_DISCOUNT',
                            'price_source_id' => $this->discount->id,
                            'quantity_scanned' => $discounted,
                        ]);
                    $newRecord->saveQuietly();
                }

                $lowestPriceRecord->updateQuietly([
                    'quantity_scanned' => $lowestPriceRecord->quantity_scanned - $discounted,
                ]);
            } else {
                if ($lowestPriceAlreadyDiscounted) {
                    $lowestPriceAlreadyDiscounted->updateQuietly([
                        'quantity_scanned' => $lowestPriceAlreadyDiscounted->quantity_scanned + $lowestPriceRecord->quantity_scanned,
                    ]);
                    $lowestPriceRecord->delete();
                } else {
                    $lowestPriceRecord->updateQuietly([
                        'unit_sold_price' => $newSoldPrice,
                        'price_source' => 'QUANTITY_DISCOUNT',
                        'price_source_id' => $this->discount->id,
                    ]);
                }
            }
        }
    }
}
