<?php

namespace Tests\Browser\Routes\Settings;

use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class DpdIrelandPageTest extends DuskTestCase
{
    private string $uri = '/settings/dpd-ireland';

    /**
     * @throws Throwable
     */
    public function testPage(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->browse(function (Browser $browser) use ($user) {
            $browser->disableFitOnFailure();
            $browser->loginAs($user);
            $browser->visit($this->uri);
            $browser->assertPathIs($this->uri);
            // $browser->assertSee('');
            $browser->assertSourceMissing('Server Error');
        });
    }

    /**
     * @throws Throwable
     */
    public function testBasics(): void
    {
        $this->basicUserAccessTest($this->uri, true);
    }
}
