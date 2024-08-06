<?php

namespace App\Modules\DataCollectorGroupRecords\src;

use App\Events\DataCollection\DataCollectionRecalculateRequestEvent;
use App\Modules\BaseModuleServiceProvider;

class DataCollectorGroupRecordsServiceProvider extends BaseModuleServiceProvider
{
    public static string $module_name = 'eCommerce - Transaction Products';

    public static string $module_description = 'Module is responsible for managing transaction products (grouping similar products in one transaction etc.)';

    public static bool $autoEnable = true;

    protected $listen = [
        DataCollectionRecalculateRequestEvent::class => [
            Listeners\DataCollectionRecalculateRequestEventListener::class,
        ],
    ];

    public static function enabling(): bool
    {
        return parent::enabling();
    }
}
