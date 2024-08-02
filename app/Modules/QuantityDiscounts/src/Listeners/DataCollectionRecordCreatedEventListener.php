<?php

namespace App\Modules\QuantityDiscounts\src\Listeners;

use App\Events\DataCollectionRecord\DataCollectionRecordCreatedEvent;
use App\Modules\QuantityDiscounts\src\Models\QuantityDiscount;

class DataCollectionRecordCreatedEventListener
{
    public function handle(DataCollectionRecordCreatedEvent $event)
    {
        $record = $event->dataCollectionRecord;

        QuantityDiscount::query()
            ->whereHas('products', function ($query) use ($record) {
                $query->whereIn('product_id', function ($subQuery) use ($record) {
                    $subQuery->select('product_id')
                        ->from('data_collection_records')
                        ->where('data_collection_id', $record->data_collection_id);
                });
            })
            ->with('products')
            ->get()
            ->each(function (QuantityDiscount $quantityDiscount) use ($record) {
                $job = new $quantityDiscount->job_class($record->dataCollection, $quantityDiscount);
                $job->handle();
            });
    }
}
