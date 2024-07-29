<?php

namespace App\Modules\QuantityDiscounts\src\Listeners;

use App\Events\DataCollectionRecord\DataCollectionRecordUpdatedEvent;
use App\Models\DataCollectionRecord;
use App\Modules\QuantityDiscounts\src\Models\QuantityDiscount;
use App\Modules\QuantityDiscounts\src\Models\QuantityDiscountsProduct;

class DataCollectionRecordUpdatedEventListener
{
    public function handle(DataCollectionRecordUpdatedEvent $event)
    {
        $record = $event->dataCollectionRecord;
        $collectionId = data_get($record, 'data_collection_id');
        if ($collectionId === null) {
            return;
        }

        $collectionRecords = DataCollectionRecord::query()
            ->where('data_collection_id', $collectionId)
            ->whereNull('price_source_id')
            ->whereNotIn('product_id', function ($query) {
                $query->select('product_id')
                    ->from('data_collection_records')
                    ->whereNotNull('price_source_id')
                    ->where('price_source', 'QUANTITY_DISCOUNT');
            })
            ->get();

        $products = QuantityDiscountsProduct::query()
            ->whereIn('product_id', $collectionRecords->pluck('product_id'))
            ->get();

        $applicableQuantityDiscounts = QuantityDiscount::query()
            ->whereIn('id', $products->pluck('quantity_discount_id'))
            ->get();

        $applicableQuantityDiscounts
            ->each(function (QuantityDiscount $quantityDiscount) use ($collectionRecords) {
                $job = new $quantityDiscount->job_class($quantityDiscount, $collectionRecords);
                $job->handle();
            });
    }
}
