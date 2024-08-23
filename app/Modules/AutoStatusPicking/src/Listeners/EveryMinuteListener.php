<?php

namespace App\Modules\AutoStatusPicking\src\Listeners;

use App\Modules\AutoStatusPicking\src\Jobs\DistributePicksJob;
use App\Modules\AutoStatusPicking\src\Jobs\UnDistributePicksJob;

class EveryMinuteListener
{
    public function handle(): void
    {
         DistributePicksJob::dispatch();
         UnDistributePicksJob::dispatch();
    }
}
