<?php

namespace App\Modules\DataCollectorSalePrices\src\Listeners;

use App\Events\DataCollection\DataCollectionRecalculateRequestEvent;
use App\Modules\DataCollectorSalePrices\src\Jobs\ApplySalePricesJob;

class DataCollectionRecalculateRequestEventListener
{
    public function handle(DataCollectionRecalculateRequestEvent $event): void
    {
        ApplySalePricesJob::dispatch($event->dataCollection);
    }
}
