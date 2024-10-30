<?php

namespace App\Modules\VatRates\src;

use App\Modules\BaseModuleServiceProvider;

class VatRatesModuleServiceProvider extends BaseModuleServiceProvider
{
    public static string $module_name = 'Vat Rates';

    public static string $module_description = 'Module provides an ability to add vat rates to the products.';

    public static string $settings_link = '/settings/modules/vat-rates';

    public static bool $autoEnable = true;

    protected $listen = [
        //
    ];

    public static function enabling(): bool
    {
        return parent::enabling();
    }
}
