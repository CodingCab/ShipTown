<?php

namespace App\Modules\AutoStatusPicking\src;

use App\Events\EveryHourEvent;
use App\Events\EveryMinuteEvent;
use App\Events\Order\OrderUpdatedEvent;
use App\Events\OrderProductPick\OrderProductPickDeletedEvent;
use App\Events\Pick\PickCreatedEvent;
use App\Events\Pick\PickDeletedEvent;
use App\Modules\AutoStatusPicking\src\Jobs\RefillPickingIfEmptyJob;
use App\Modules\BaseModuleServiceProvider;

/**
 * Class AutoStatusPickingServiceProvider.
 */
class AutoStatusPickingServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = 'Automation - Auto "picking" refilling';

    public static string $module_description = '"paid" to "picking" batch refill prioritizing old orders';

    public static bool $autoEnable = false;

    protected $listen = [
        PickCreatedEvent::class => [
            Listeners\PickCreatedListener::class,
        ],

        PickDeletedEvent::class => [
            Listeners\PickDeletedListener::class,
        ],

        EveryMinuteEvent::class => [
            Listeners\EveryMinuteListener::class,
        ],

        EveryHourEvent::class => [
            Listeners\HourlyEvent\RefillPickingIfEmpty::class,
        ],

        OrderUpdatedEvent::class => [
            Listeners\OrderUpdatedEvent\RefillPickingIfEmpty::class,
        ],
    ];

    public static function enabling(): bool
    {
        RefillPickingIfEmptyJob::dispatch();

        return true;
    }
}
