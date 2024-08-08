<?php

namespace App\Modules\QuantityDiscounts\src\Listeners;

use App\Events\DataCollectionRecord\DataCollectionRecordCreatedEvent;
use App\Modules\QuantityDiscounts\src\Models\QuantityDiscount;
use App\Modules\QuantityDiscounts\src\Services\QuantityDiscountsService;

class DataCollectionRecordCreatedEventListener
{
    public function handle(DataCollectionRecordCreatedEvent $event): void
    {
        QuantityDiscountsService::dispatchQuantityDiscountJobs($event->dataCollectionRecord);
    }
}
