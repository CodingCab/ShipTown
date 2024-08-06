<?php

namespace App\Modules\DataCollectorGroupRecords\src\Listeners;

use App\Events\DataCollection\DataCollectionRecalculateRequestEvent;
use App\Modules\DataCollectorGroupRecords\src\Jobs\GroupSimilarProducts;

class DataCollectionRecalculateRequestEventListener
{
    public function handle(DataCollectionRecalculateRequestEvent $event)
    {
        GroupSimilarProducts::dispatchSync($event->dataCollection);
    }
}
