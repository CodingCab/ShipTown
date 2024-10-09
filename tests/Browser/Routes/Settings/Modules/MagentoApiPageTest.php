<?php

namespace Tests\Browser\Routes\Settings\Modules;

use App\Modules\MagentoApi\src\EventServiceProviderBase;
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
        EventServiceProviderBase::enableModule();

        $this->basicAdminAccessTest($this->uri, true);
    }
}
