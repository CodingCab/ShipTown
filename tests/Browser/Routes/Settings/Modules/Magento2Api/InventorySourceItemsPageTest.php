<?php

namespace Tests\Browser\Routes\Settings\Modules\Magento2Api;

use App\User;
use Tests\DuskTestCase;
use Throwable;

class InventorySourceItemsPageTest extends DuskTestCase
{
    private string $uri = '/settings/modules/magento2api/inventory-source-items';

    /**
     * @throws Throwable
     */
    public function testPage()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->visitAndInspect($this->uri, $user)
            ->assertPathIs($this->uri);
    }
}