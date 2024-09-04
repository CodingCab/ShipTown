<?php

namespace App\Modules\PrintNode\src;

use App\Events\PrintJob\PrintJobCreatedEvent;
use App\Events\ShippingLabel\ShippingLabelCreatedEvent;
use App\Modules\BaseModuleServiceProvider;

class PrintNodeServiceProvider extends BaseModuleServiceProvider
{
    public static string $module_name = 'Printer - PrintNode Integration';

    public static string $module_description = 'Print labels directly to your own printer';

    public static string $settings_link = '/admin/settings/printnode';

    public static bool $autoEnable = false;

    /**
     * @var array
     */
    protected $listen = [
        ShippingLabelCreatedEvent::class => [
            Listeners\PrintShippingLabelListener::class,
        ],

        PrintJobCreatedEvent::class => [
            Listeners\PrintJobListener::class,
        ],
    ];
}
