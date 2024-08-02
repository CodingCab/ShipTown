<?php

namespace App\Modules\QuantityDiscounts\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Models\DataCollection;
use App\Models\DataCollectionRecord;
use App\Modules\QuantityDiscounts\src\Models\QuantityDiscount;
use Illuminate\Database\Eloquent\Collection;
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

    public function __construct(QuantityDiscount $discount, DataCollection $dataCollection)
    {
        $this->discount = $discount;
        $this->dataCollection = $dataCollection;
    }

    public function handle(): void
    {
        $key = implode('-', ['quantity_discounts_data_collection_record_updated_event', $this->dataCollection->id]);

        Cache::lock($key, 5)->get(function () {
            $this->applyDiscount();
        });
    }

    public function applyDiscount(): void
    {
        $discountConfig = $this->discount->configuration;

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

        $totalQuantityScanned = $productIncludedInDiscount->sum('quantity_scanned');
        $quantityAtFullPrice = (int)data_get($discountConfig, 'quantity_full_price', 0);
        $quantityAtDiscountedPrice = (int)data_get($discountConfig, 'quantity_discounted', 0);

        $quantityRequiredPerOffer = $quantityAtFullPrice + $quantityAtDiscountedPrice;

        $totalQuantityDiscounted = $quantityAtDiscountedPrice * intdiv($totalQuantityScanned, $quantityRequiredPerOffer);

        $this->extractPromotionalProducts($productIncludedInDiscount, $totalQuantityDiscounted);
    }

    public function extractPromotionalProducts(Collection $filteredCollectionRecords, int $totalQuantityRequired): mixed
    {
        $remainingQuantityToDiscount = $totalQuantityRequired;

        $filteredCollectionRecords->each(function (DataCollectionRecord $record) use (&$remainingQuantityToDiscount) {
            $quantityToExtract = min($record->quantity_scanned, $remainingQuantityToDiscount);

            if ($quantityToExtract == 0) {
                if ($record->price_source_id === $this->discount->id) {
                    $record->update([
                        'price_source' => null,
                        'price_source_id' => null,
                        'unit_sold_price' => $record->unit_full_price,
                    ]);
                }
            } else if ($quantityToExtract == $record->quantity_scanned) {
                if ($record->price_source_id === null) {
                    $record->update([
                        'price_source' => "QUANTITY_DISCOUNT",
                        'price_source_id' => $this->discount->id,
                        'unit_sold_price' => $record->unit_full_price * ($this->discount->configuration['discount_percent'] / 100),
                    ]);
                }
            } else if ($quantityToExtract < $record->quantity_scanned) {
                $quantityToCarryOver = $record->quantity_scanned - $quantityToExtract;

                $record->update([
                    'quantity_scanned' => $quantityToExtract,
                    'unit_sold_price' => $record->unit_full_price * ($this->discount->configuration['discount_percent'] / 100),
                    'price_source' => "QUANTITY_DISCOUNT",
                    'price_source_id' => $this->discount->id,
                ]);

                $this->dataCollection
                    ->firstOrCreateProductRecord($record->product_id, $record->unit_full_price)
                    ->increment('quantity_scanned', $quantityToCarryOver);
            }

            $remainingQuantityToDiscount -= $quantityToExtract;

            return true;
        });

        return $totalQuantityRequired;
    }
}
