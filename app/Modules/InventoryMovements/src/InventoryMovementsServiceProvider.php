<?php

namespace App\Modules\InventoryMovements\src;

use App\Events\EveryHourEvent;
use App\Events\EveryMinuteEvent;
use App\Events\InventoryMovement\InventoryMovementCreatedEvent;
use App\Modules\BaseModuleServiceProvider;

/**
 * Class InventoryQuantityReservedServiceProvider.
 */
class InventoryMovementsServiceProvider extends BaseModuleServiceProvider
{
    public static string $module_name = '.CORE - Inventory Movements';

    public static string $module_description = 'Monitor inventory movements and ensure inventory quantities quantities are correct.';

    public static bool $autoEnable = true;

    /**
     * @var array
     */
    protected $listen = [
        InventoryMovementCreatedEvent::class => [
            Listeners\InventoryMovementCreatedEventListener::class,
        ],

        EveryMinuteEvent::class => [
            Listeners\EveryMinuteEventListener::class,
        ],

        EveryHourEvent::class => [
            Listeners\EveryHourEventListener::class,
        ],
    ];

    public static function disabling(): bool
    {
        return true;
    }
}
