<?php

namespace App\Modules\QuantityDiscounts\src\Listeners;

use App\Events\DataCollectionRecord\DataCollectionRecordUpdatedEvent;
use App\Models\DataCollectionRecord;
use Illuminate\Support\Facades\Log;

class DataCollectionRecordUpdatedEventListener
{
    public function handle(DataCollectionRecordUpdatedEvent $event)
    {
        $record = $event->dataCollectionRecord;
        Log::debug('DataCollectionRecordUpdatedEventListener', ['record' => $record->dataCollection]);

        if ($record->dataCollection->type === DataCollectionRecord::class) {
        }
    }
}
