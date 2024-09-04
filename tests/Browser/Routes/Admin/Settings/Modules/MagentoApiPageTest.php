<?php

namespace Tests\Browser\Routes\Admin\Settings\Modules;

use App\Modules\MagentoApi\src\EventServiceProviderBase;
use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class MagentoApiPageTest extends DuskTestCase
{
    private string $uri = '/admin/settings/modules/magento-api';

    public function testBasics()
    {
        EventServiceProviderBase::enableModule();

        $this->basicAdminAccessTest($this->uri, true);
        $this->basicUserAccessTest($this->uri, false);
        $this->basicGuestAccessTest($this->uri);
    }

    public function testPage()
    {
        EventServiceProviderBase::enableModule();

        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit($this->uri)
                ->assertSee('Magento Api Configurations');
        });
    }
}
