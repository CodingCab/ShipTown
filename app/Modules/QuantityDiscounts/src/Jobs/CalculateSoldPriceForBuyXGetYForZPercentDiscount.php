<?php

namespace App\Modules\QuantityDiscounts\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Models\DataCollection;
use App\Models\DataCollectionRecord;
use App\Modules\QuantityDiscounts\src\Models\QuantityDiscount;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CalculateSoldPriceForBuyXGetYForZPercentDiscount extends UniqueJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private QuantityDiscount $discount;
    private DataCollection $dataCollection;

    public function uniqueId(): string
    {
        return implode('_', [self::class, $this->dataCollection->id]);
    }

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(QuantityDiscount $discount, DataCollection $dataCollection)
    {
        $this->discount = $discount;
        $this->dataCollection = $dataCollection;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        ray()->clearAll();

        ray('CalculateSoldPriceForBuyXGetYForZPercentDiscount');

        $discountConfig = $this->discount->configuration;
        $discountProducts = $this->discount->products()->with('product')->get();

        $this->dataCollection->records()
            ->whereIn('product_id', $discountProducts->pluck('product_id'))
            ->where(['price_source_id' => $this->discount->id])
            ->update([
                'price_source' => null,
                'price_source_id' => null,
                'unit_sold_price' => DB::raw('unit_full_price')
            ]);

        $filteredCollectionRecords = $this->dataCollection->records()
            ->whereIn('product_id', $discountProducts->pluck('product_id'))
            ->where(function ($query) {
                $query->whereNull('price_source_id')
                    ->orWhere(['price_source_id' => $this->discount->id]);
            })
            ->orderBy('unit_full_price', 'ASC')
            ->get();

        $totalQuantityScanned = $filteredCollectionRecords->sum('quantity_scanned');

        $requiredQuantity = (int)data_get($discountConfig, 'quantity_full_price', 0);
        $discountedQuantity = (int)data_get($discountConfig, 'quantity_discounted', 0);
        $discountPercent = (int)data_get($discountConfig, 'discount_percent', 0);
        $totalQuantityRequired = $requiredQuantity + $discountedQuantity;

        $multiplication = intdiv($totalQuantityScanned, $totalQuantityRequired);

        ray(['totalQuantityScanned' => $totalQuantityScanned, 'multiplication' => $multiplication]);
        if ($totalQuantityScanned < ($multiplication * $totalQuantityRequired)) {
            return;
        }

        $this->extractPromotionalProducts($filteredCollectionRecords, ($multiplication * $totalQuantityRequired));
        $this->updatePromotionalPrices($multiplication * $discountedQuantity);
    }

    public function updatePromotionalPrices($quantityToDiscount): void
    {
        $discountedQuantity = $quantityToDiscount;

        $this->dataCollection->records()
            ->where('price_source_id', $this->discount->id)
            ->update(['unit_sold_price' => DB::raw('unit_full_price')]);

        $filteredCollectionRecords = $this->dataCollection->records()
            ->where('price_source_id', $this->discount->id)
            ->where('quantity_scanned', '!=', 0)
            ->orderBy('unit_full_price', 'ASC')
            ->get();

        $filteredCollectionRecords->each(function (DataCollectionRecord $record) use (&$discountedQuantity) {
            $quantityToExtract = min($record->quantity_scanned, $discountedQuantity);

            ray($discountedQuantity, $quantityToExtract);


            if ($quantityToExtract < 0.01) {
                return true;
            }

            if ($quantityToExtract === $record->quantity_scanned) {
                $record->update([
                    'unit_sold_price' => $record->unit_full_price * ($this->discount->configuration['discount_percent'] / 100),
                ]);

                $discountedQuantity -= $quantityToExtract;
            } else {
                ray($record, $quantityToExtract);
                $newRecord = $record->replicate([
                    'quantity_to_scan',
                    'unit_discount',
                    'total_discount',
                    'total_sold_price',
                    'total_cost',
                    'total_price',
                    'total_profit',
                    'total_full_price',
                    'is_requested',
                    'is_fully_scanned',
                    'is_over_scanned',
                    'total_transferred_in',
                    'total_transferred_out',
                    'quantity_scanned'
                ]);
                $newRecord->fill([
                    'unit_sold_price' => $record->unit_full_price * ($this->discount->configuration['discount_percent'] / 100),
                    'price_source' => 'QUANTITY_DISCOUNT',
                    'price_source_id' => $this->discount->id,
                    'quantity_scanned' => $quantityToExtract,
                ])
                ->save();

                $record->update([
                    'quantity_scanned' => $record->quantity_scanned - $quantityToExtract,
                ]);

                $discountedQuantity -= $quantityToExtract;
            }

            if ($discountedQuantity < 0.01) {
                return false;
            }

            return true;
        });
    }

    /**
     * @param Collection $filteredCollectionRecords
     * @param int $totalQuantityRequired
     * @return float|int|mixed
     */
    public function extractPromotionalProducts(Collection $filteredCollectionRecords, int $totalQuantityRequired): mixed
    {
        $filteredCollectionRecords->each(function (DataCollectionRecord $record) use (&$totalQuantityRequired) {
            $quantityToExtract = min($record->quantity_scanned, $totalQuantityRequired);
            ray([
                '$quantityToExtract' => $quantityToExtract,
                'record_quantity_scanned' => $record->quantity_scanned,
                'totalQuantityRequired' => $totalQuantityRequired,
            ]);

            if ($totalQuantityRequired === 0) {
                return false;
            }

            if ($quantityToExtract === $record->quantity_scanned) {
                $record->update([
                    'price_source' => 'QUANTITY_DISCOUNT',
                    'price_source_id' => $this->discount->id,
                ]);

                $totalQuantityRequired -= $quantityToExtract;
            } elseif ($quantityToExtract > 0) {
                $newRecord = $record->replicate([
                    'quantity_to_scan',
                    'unit_discount',
                    'total_discount',
                    'total_sold_price',
                    'total_cost',
                    'total_price',
                    'total_profit',
                    'total_full_price',
                    'is_requested',
                    'is_fully_scanned',
                    'is_over_scanned',
                    'total_transferred_in',
                    'total_transferred_out',
                    'quantity_scanned'
                ]);
                $newRecord->fill([
                    'price_source' => 'QUANTITY_DISCOUNT',
                    'price_source_id' => $this->discount->id,
                    'quantity_scanned' => $quantityToExtract,
                ])
                    ->save();

                $record->update([
                    'quantity_scanned' => $record->quantity_scanned - $quantityToExtract,
                ]);

                $totalQuantityRequired -= $quantityToExtract;
            } else {
                $record->update([
                    'price_source' => null,
                    'price_source_id' => null,
                ]);
            }

            return true;
        });

        return $totalQuantityRequired;
    }
}
