<?php

namespace App\Modules\Picklist\src\Listeners;

use App\Events\Pick\PickDeletedEvent;
use App\Models\OrderProductPick;
use App\Modules\Picklist\src\Jobs\UnDistributePicksJob;

class PickDeletedListener
{
    public function handle(PickDeletedEvent $event): void
    {
        OrderProductPick::query()->where('pick_id', $event->pick->id)->delete();

        UnDistributePicksJob::dispatch();
    }
}
