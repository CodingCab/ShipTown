<?php

namespace App\Modules\QuantityDiscounts\src\Listeners;

use App\Events\DataCollectionRecord\DataCollectionRecordDeletedEvent;
use App\Modules\QuantityDiscounts\src\Services\QuantityDiscountsService;

class DataCollectionRecordDeletedEventListener
{
    public function handle(DataCollectionRecordDeletedEvent $event): void
    {
        QuantityDiscountsService::dispatchQuantityDiscountJobs($event->dataCollectionRecord);
    }
}
