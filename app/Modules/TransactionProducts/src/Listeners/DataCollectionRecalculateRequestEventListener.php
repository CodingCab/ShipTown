<?php

namespace App\Modules\TransactionProducts\src\Listeners;

use App\Events\DataCollection\DataCollectionRecalculateRequestEvent;
use App\Modules\DataCollector\src\Jobs\RecountTotalsJob;
use App\Modules\TransactionProducts\src\Jobs\GroupSimilarProducts;

class DataCollectionRecalculateRequestEventListener
{
    public function handle(DataCollectionRecalculateRequestEvent $event)
    {
        $job = new GroupSimilarProducts($event->dataCollection);
        $job->handle();
    }
}
