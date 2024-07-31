<?php

namespace App\Modules\DataCollector\src\Listeners;

use App\Models\DataCollection;
use App\Modules\DataCollector\src\Jobs\RecountTotalsDataCollectionsJob;

class EveryMinuteEventListener
{
    public function handle(): void
    {
        RecountTotalsDataCollectionsJob::dispatch();
    }
}
