<?php

namespace App\Modules\TransactionProducts\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Models\DataCollection;
use Illuminate\Support\Facades\Cache;

class GroupSimilarProducts extends UniqueJob
{
    private DataCollection $dataCollection;

    public function uniqueId(): string
    {
        return implode('_', [self::class, $this->dataCollection->id]);
    }

    public function __construct(DataCollection $dataCollection)
    {
        $this->dataCollection = $dataCollection;
    }

    public function handle(): void
    {
        $cacheLockKey = implode('-', ['grouping_similar_transaction_products_for_data_collection', $this->dataCollection->id]);

        Cache::lock($cacheLockKey, 5)->get(function () {
            $this->groupSimilarProducts();
        });
    }

    public function groupSimilarProducts(): void
    {
        $groupedRecords = $this->dataCollection->records->groupBy(function ($item) {
            return $item['product_id'] . '|' . $item['unit_sold_price'] . '|' . $item['price_source'] . '|' . $item['price_source_id'];
        });

        $groupedRecords->each(function ($items) {
            if ($items->count() === 1) {
                return true;
            }

            $firstItem = $items->first();
            $firstItem->quantity_scanned = max($items->sum('quantity_scanned'), $items->count());
            $firstItem->saveQuietly();

            $items->slice(1)->each->deleteQuietly();
            return true;
        });
    }
}
