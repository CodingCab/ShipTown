<?php

namespace Tests\Browser\Routes\Reports;

use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class InventoryReservationsPageTest extends DuskTestCase
{
    private string $uri = '/reports/inventory-reservations';

    /**
     * @throws Throwable
     */
    public function testPage()
    {
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
