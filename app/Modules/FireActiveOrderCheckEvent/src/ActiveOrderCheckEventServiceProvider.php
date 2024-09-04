<?php

namespace App\Modules\FireActiveOrderCheckEvent\src;

use App\Modules\BaseModuleServiceProvider;

/**
 * Class InventoryQuantityReservedServiceProvider.
 */
class ActiveOrderCheckEventServiceProvider extends BaseModuleServiceProvider
{
    public static string $module_name = '.CORE - Active Order Check';

    public static string $module_description = 'Run automations 5 minutes after the order is created';

    public static bool $autoEnable = true;

    /**
     * @var array
     */
    protected $listen = [
    ];

    public static function disabling(): bool
    {
        return false;
    }
}
