<?php

namespace App\Modules\AutoStatusPicking\src\Listeners;

use App\Events\Pick\PickDeletedEvent;
use App\Models\OrderProductPick;
use App\Modules\AutoStatusPicking\src\Jobs\UnDistributePicksJob;

class PickDeletedListener
{
    public function handle(PickDeletedEvent $event): void
    {
        OrderProductPick::query()->where('pick_id', $event->pick->id)->delete();

        UnDistributePicksJob::dispatch();
    }
}
