<?php

namespace App\Modules\DataCollectorGroupRecords\src\Jobs;

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
        $cacheLockKey = implode('-', ['grouping_similar_records_in_data_collection', $this->dataCollection->id]);

        Cache::lock($cacheLockKey, 5)->get(function () {
            $this->groupSimilarProducts();
        });
    }

    public function groupSimilarProducts(): void
    {
        $groupedRecords = $this->dataCollection->records()
            ->whereNull('deleted_at')
            ->get()
            ->groupBy(function ($item) {
                return $item['product_id'] . '|' . $item['unit_sold_price'] . '|' . $item['price_source'] . '|' . $item['price_source_id'] . '|' . $item['unit_cost'] . '|' . $item['custom_uuid'];
            });

        $groupedRecords->each(function ($items) {
            if ($items->count() === 1) {
                return true;
            }

            $items->first()
                ->replicate()
                ->fill([
                    'quantity_scanned' => $items->sum('quantity_scanned'),
                    'total_transferred_in' => $items->sum('total_transferred_in'),
                    'total_transferred_out' => $items->sum('total_transferred_out'),
                    'quantity_requested' => $items->sum('quantity_requested'),
                ])
                ->saveQuietly();

            $items->each(function ($item) {
                $item->deleteQuietly();
            });

            return true;
        });
    }
}
