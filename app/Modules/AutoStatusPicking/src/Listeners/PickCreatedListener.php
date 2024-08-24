<?php

namespace App\Modules\AutoStatusPicking\src\Listeners;

use App\Modules\AutoStatusPicking\src\Jobs\DistributePicksJob;

class PickCreatedListener
{
    public function handle($event): void
    {
        DistributePicksJob::dispatch($event->pick);
    }
}
