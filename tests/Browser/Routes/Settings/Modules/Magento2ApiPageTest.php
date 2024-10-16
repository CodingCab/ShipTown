<?php

namespace Tests\Browser\Routes\Settings\Modules;

use App\User;
use Tests\DuskTestCase;
use Throwable;

class Magento2ApiPageTest extends DuskTestCase
{
    private string $uri = '/settings/modules/magento2api';

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
