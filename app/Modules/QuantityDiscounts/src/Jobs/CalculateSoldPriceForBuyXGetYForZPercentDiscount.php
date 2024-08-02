<?php

namespace App\Modules\QuantityDiscounts\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Models\DataCollection;
use App\Models\DataCollectionRecord;
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
        $quantityAtFullPrice        = (int)data_get($this->discount->configuration, 'quantity_full_price', 0);
        $quantityAtDiscountedPrice  = (int)data_get($this->discount->configuration, 'quantity_discounted', 0);
        $quantityRequiredPerOffer   = $quantityAtFullPrice + $quantityAtDiscountedPrice;

        $totalQuantityScanned           = $productIncludedInDiscount->sum('quantity_scanned');
        $timesWeCanApplyPromotion       = intdiv($totalQuantityScanned, $quantityRequiredPerOffer);
        $totalQuantityAtDiscountedPrice = $quantityAtDiscountedPrice * $timesWeCanApplyPromotion;

        $remainingQuantityToDiscount = $totalQuantityAtDiscountedPrice;

        $productIncludedInDiscount->each(function (DataCollectionRecord $record) use (&$remainingQuantityToDiscount) {
            $quantityToDiscount = min($record->quantity_scanned, $remainingQuantityToDiscount);

            $remainingQuantityToDiscount -= $quantityToDiscount;

            if ($quantityToDiscount == 0) {
                if ($record->price_source_id === $this->discount->id) {
                    $record->update([
                        'price_source' => null,
                        'price_source_id' => null,
                        'unit_sold_price' => $record->unit_full_price,
                    ]);
                }
            } else if ($quantityToDiscount == $record->quantity_scanned) {
                if ($record->price_source_id === null) {
                    $record->update([
                        'price_source' => "QUANTITY_DISCOUNT",
                        'price_source_id' => $this->discount->id,
                        'unit_sold_price' => $record->unit_full_price * ($this->discount->configuration['discount_percent'] / 100),
                    ]);
                }
            } else if ($quantityToDiscount < $record->quantity_scanned) {
                $quantityToCarryOver = $record->quantity_scanned - $quantityToDiscount;

                $record->update([
                    'quantity_scanned' => $quantityToDiscount,
                    'unit_sold_price' => $record->unit_full_price * ($this->discount->configuration['discount_percent'] / 100),
                    'price_source' => "QUANTITY_DISCOUNT",
                    'price_source_id' => $this->discount->id,
                ]);

                $this->dataCollection
                    ->firstOrCreateProductRecord($record->product_id, $record->unit_full_price)
                    ->increment('quantity_scanned', $quantityToCarryOver);
            }


            return true;
        });
    }
}
