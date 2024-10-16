<?php

namespace Tests\Browser\Routes\Settings\Modules;

use App\Modules\Magento2API\PriceSync\src\PriceSyncServiceProvider;
use App\User;
use Tests\DuskTestCase;
use Throwable;

class MagentoApiPageTest extends DuskTestCase
{
    private string $uri = '/settings/modules/magento-api';

    /**
     * @throws Throwable
     */
    public function testBasics(): void
    {
        PriceSyncServiceProvider::enableModule();

        $admin = User::factory()->create()->assignRole('admin');

        $this->visitAndInspect($this->uri, $admin)
            ->waitForText('Magento Api Configurations');
    }
}
