<?php

namespace App\Modules\InventoryReservations\src;

use App\Events\Inventory\RecalculateInventoryRequestEvent;
use App\Events\Order\OrderUpdatedEvent;
use App\Events\OrderProduct\OrderProductCreatedEvent;
use App\Events\OrderProduct\OrderProductUpdatedEvent;
use App\Models\Warehouse;
use App\Modules\BaseModuleServiceProvider;
use App\Modules\InventoryReservations\src\Models\Configuration;

class EventServiceProviderBase extends BaseModuleServiceProvider
{
    public static string $module_name = '.CORE - Inventory Reservations';

    public static string $module_description = 'Reserves stock for active orders.';

    public static string $settings_link = '/admin/settings/modules/inventory-reservations';

    public static bool $autoEnable = false;

    protected $listen = [];

    public static function enableModule(): bool
    {
        return parent::enableModule();
    }

    public static function disabling(): bool
    {
        return false;
    }
}
