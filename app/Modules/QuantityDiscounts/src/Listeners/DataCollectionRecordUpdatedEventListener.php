<?php

namespace App\Modules\QuantityDiscounts\src\Listeners;

use App\Events\DataCollectionRecord\DataCollectionRecordUpdatedEvent;
use App\Models\DataCollectionRecord;
use App\Modules\QuantityDiscounts\src\Models\QuantityDiscount;

class DataCollectionRecordUpdatedEventListener
{
    public function handle(DataCollectionRecordUpdatedEvent $event)
    {
        $record = $event->dataCollectionRecord;
        $collectionId = data_get($record, 'data_collection_id');
        if ($collectionId === null) {
            return;
        }

        $dispatcher = DataCollectionRecord::getEventDispatcher();
        DataCollectionRecord::unsetEventDispatcher();

        $applicableQuantityDiscounts = QuantityDiscount::query()
            ->whereHas('products', function ($query) use ($collectionId) {
                $query->whereIn('product_id', function ($subQuery) use ($collectionId) {
                    $subQuery->select('product_id')
                        ->from('data_collection_records')
                        ->where('data_collection_id', $collectionId);
                });
            })
            ->with('products')
            ->get();

        $applicableQuantityDiscounts
            ->each(function (QuantityDiscount $quantityDiscount) use ($record) {
                $job = new $quantityDiscount->job_class($quantityDiscount, $record->dataCollection);
                $job->handle();
            });

        DataCollectionRecord::setEventDispatcher($dispatcher);
    }
}
