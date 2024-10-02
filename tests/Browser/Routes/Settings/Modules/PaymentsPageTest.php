<?php

namespace Tests\Browser\Routes\Settings\Modules;

use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class PaymentsPageTest extends DuskTestCase
{
    private string $uri = '/settings/modules/payments';

    /**
     * @throws Throwable
     */
    public function testBasics()
    {
        $this->basicUserAccessTest($this->uri, true);
        $this->basicAdminAccessTest($this->uri, true);
        $this->basicGuestAccessTest($this->uri);
    }

    /**
     * @throws Throwable
     */
    public function testPage()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->browse(function (Browser $browser) use ($user) {
            $browser->disableFitOnFailure()
                ->loginAs($user)
                ->visit($this->uri)
                ->assertPathIs($this->uri)
                ->assertSourceMissing('Server Error');
        });
    }
}
