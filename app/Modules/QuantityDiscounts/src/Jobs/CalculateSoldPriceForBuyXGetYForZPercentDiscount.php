<?php

namespace App\Modules\QuantityDiscounts\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Models\DataCollection;
use App\Models\DataCollectionRecord;
use App\Modules\DataCollector\src\Services\DataCollectorService;
use App\Modules\QuantityDiscounts\src\Models\QuantityDiscount;
use Illuminate\Support\Arr;
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
        $key = implode('-', ['quantity_discounts_data_collection_record_updated_event', $this->dataCollection->id]);

        Cache::lock($key, 5)->get(function () {
            $productIncludedInDiscount = $this->dataCollection->records()
                ->whereIn('product_id', Arr::pluck($this->discount->products, 'product_id'))
                ->where(function ($query) {
                    $query->whereNull('price_source_id')
                        ->orWhere(['price_source_id' => $this->discount->id]);
                })
                ->orderBy('unit_full_price', 'ASC')
                ->orderBy('price_source', 'DESC')
                ->orderBy('quantity_scanned', 'DESC')
                ->orderBy('id', 'ASC')
                ->get();

            $this->applyQuantityDiscount($productIncludedInDiscount);
        });
    }

    public function applyQuantityDiscount($productIncludedInDiscount): void
    {
        $totalQuantityScanned           = $productIncludedInDiscount->sum('quantity_scanned');

        $quantityAtFullPrice        = (int)data_get($this->discount->configuration, 'quantity_full_price', 0);
        $quantityAtDiscountedPrice  = (int)data_get($this->discount->configuration, 'quantity_discounted', 0);
        $quantityRequiredPerOffer   = $quantityAtFullPrice + $quantityAtDiscountedPrice;

        $timesWeCanApplyPromotion   = intdiv($totalQuantityScanned, $quantityRequiredPerOffer);
        $totalQuantityToIncludeInPromotion = $quantityRequiredPerOffer * $timesWeCanApplyPromotion;

        $remainingQuantityToIncludeInPromotion = $totalQuantityToIncludeInPromotion;

        ray([
            'quantityAtFullPrice' => $quantityAtFullPrice,
            'quantityAtDiscountedPrice' => $quantityAtDiscountedPrice,
            'quantityRequiredPerOffer' => $quantityRequiredPerOffer,
            'totalQuantityScanned' => $totalQuantityScanned,
            'timesWeCanApplyPromotion' => $timesWeCanApplyPromotion,
            'remainingQuantityToIncludeInPromotion' => $remainingQuantityToIncludeInPromotion,
        ]);

        $productIncludedInDiscount->each(function (DataCollectionRecord $record) use (&$remainingQuantityToIncludeInPromotion) {
            $quantityToDiscount = min($record->quantity_scanned, $remainingQuantityToIncludeInPromotion);

            ray([
                'record' => $record->toArray(),
                'sku' => $record->product->sku,
                'quantity_scanned' => $record->quantity_scanned,
                'quantityToDiscountThisRecord' => $quantityToDiscount,
                'remainingQuantityToIncludeInPromotion' => $remainingQuantityToIncludeInPromotion,
            ]);

            if ($record->quantity_scanned == 0) {
                return true;
            }

            // when there is nothing left to discount
            if ($quantityToDiscount == 0) {
                // and when hast already applied discount
                if ($record->price_source_id === $this->discount->id) {
                    ray(['resetting price source', $record->toArray()]);
                    $record->update([
                        'price_source' => null,
                        'price_source_id' => null,
                    ]);
                }
            } else if ($quantityToDiscount > 0 && $quantityToDiscount <= $record->quantity_scanned) {
                ray('splitting quantity', $record->toArray());
                $quantityToCarryOver = $record->quantity_scanned - $quantityToDiscount;
                $discountedPrice = $record->unit_full_price * ($this->discount->configuration['discount_percent'] / 100);

                $product1 = [
                    'price_source' => null,
                    'price_source_id' => null,
                ];

                $discountedAttributes = [
                    'unit_sold_price' => $discountedPrice,
                    'price_source' => "QUANTITY_DISCOUNT",
                    'price_source_id' => $this->discount->id,
                ];

                DataCollectorService::splitRecord($record, $quantityToDiscount);

                $remainingQuantityToIncludeInPromotion -= $quantityToCarryOver;
            }

            return true;
        });
    }
    public function applyQuantityDiscountBck($productIncludedInDiscount): void
    {
        $quantityAtFullPrice        = (int)data_get($this->discount->configuration, 'quantity_full_price', 0);
        $quantityAtDiscountedPrice  = (int)data_get($this->discount->configuration, 'quantity_discounted', 0);
        $quantityRequiredPerOffer   = $quantityAtFullPrice + $quantityAtDiscountedPrice;

        $totalQuantityScanned           = $productIncludedInDiscount->sum('quantity_scanned');
        $timesWeCanApplyPromotion       = intdiv($totalQuantityScanned, $quantityRequiredPerOffer);
        $totalQuantityToIncludeInPromotion = $quantityRequiredPerOffer * $timesWeCanApplyPromotion;
        $totalQuantityAtDiscountedPrice = $quantityAtDiscountedPrice * $timesWeCanApplyPromotion;

        $remainingQuantityToDiscount = $totalQuantityAtDiscountedPrice;
        $remainingQuantityToIncludeInPromotion = $totalQuantityToIncludeInPromotion;

        ray([
            'quantityAtFullPrice' => $quantityAtFullPrice,
            'quantityAtDiscountedPrice' => $quantityAtDiscountedPrice,
            'quantityRequiredPerOffer' => $quantityRequiredPerOffer,
            'totalQuantityScanned' => $totalQuantityScanned,
            'timesWeCanApplyPromotion' => $timesWeCanApplyPromotion,
            'totalQuantityAtDiscountedPrice' => $totalQuantityAtDiscountedPrice,
            'remainingQuantityToDiscount' => $remainingQuantityToDiscount,
            'remainingQuantityToIncludeInPromotion' => $remainingQuantityToIncludeInPromotion,
        ]);

        $productIncludedInDiscount->each(function (DataCollectionRecord $record) use (&$remainingQuantityToDiscount, &$remainingQuantityToIncludeInPromotion) {
            $quantityToDiscount = min($record->quantity_scanned, $remainingQuantityToDiscount);

            ray([
                'record' => $record->toArray(),
                'sku' => $record->product->sku,
                'quantity_scanned' => $record->quantity_scanned,
                'quantityToDiscountThisRecord' => $quantityToDiscount,
                'remainingQuantityToDiscount' => $remainingQuantityToDiscount,
                'remainingQuantityToIncludeInPromotion' => $remainingQuantityToIncludeInPromotion,
            ]);

            if ($record->quantity_scanned == 0) {
                return true;
            }

            // when there is nothing left to discount
            if ($quantityToDiscount == 0) {
                // and when hast already applied discount
                if ($record->price_source_id === $this->discount->id) {
                    ray(['resetting price source', $record->toArray()]);
                    $record->update([
                        'unit_sold_price' => $record->unit_full_price,
                        'price_source' => null,
                        'price_source_id' => null,
                    ]);
                }
            } else if ($quantityToDiscount > 0 && $quantityToDiscount <= $record->quantity_scanned) {
                ray('splitting quantity', $record->toArray());
                $quantityToCarryOver = $record->quantity_scanned - $quantityToDiscount;
                $discountedPrice = $record->unit_full_price * ($this->discount->configuration['discount_percent'] / 100);

                $product1 = [
                    'unit_sold_price' => $record->unit_full_price,
                    'price_source' => null,
                    'price_source_id' => null,
                ];

                $discountedAttributes = [
                    'unit_sold_price' => $discountedPrice,
                    'price_source' => "QUANTITY_DISCOUNT",
                    'price_source_id' => $this->discount->id,
                ];
//
//                if ($record->quantity_scanned == $quantityToDiscount) {
//                    $record->update($discountedAttributes);
//
//                    $remainingQuantityToDiscount -= $quantityToDiscount;
//                    $remainingQuantityToIncludeInPromotion -= $quantityToCarryOver;
//                } else if ($record->quantity_scanned > $quantityToDiscount) {
                    DataCollectorService::splitRecord($record, $quantityToDiscount, $product1, $discountedAttributes);

                    $remainingQuantityToDiscount -= $quantityToDiscount;
                    $remainingQuantityToIncludeInPromotion -= $quantityToCarryOver;
//                }
            }

            return true;
        });
    }
}
