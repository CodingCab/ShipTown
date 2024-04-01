<?php

namespace App\Modules\AddressLabel\src;

use App\Models\ShippingService;
use App\Modules\BaseModuleServiceProvider;

class BillingAddressLabelServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = '.CORE - Courier - Billing Address Label';

    /**
     * @var string
     */
    public static string $module_description = 'Allows to generate generic billing address label for the order';

    /**
     * @var bool
     */
    public static bool $autoEnable = true;

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [];

    public static function enabling(): bool
    {
        ShippingService::query()
            ->updateOrCreate([
                'code' => 'address_label',
            ], [
                'service_provider_class' => Services\BillingAddressLabelShippingService::class,
            ]);

        return true;
    }

    public static function disabling(): bool
    {
        return false;
    }
}
