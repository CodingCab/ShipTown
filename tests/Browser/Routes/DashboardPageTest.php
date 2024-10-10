<?php

namespace Tests\Browser\Routes;

use App\Models\Configuration;
use Tests\DuskTestCase;
use Throwable;

class DashboardPageTest extends DuskTestCase
{
    private string $uri = '/dashboard';

    protected function setUp(): void
    {
        parent::setUp();

        Configuration::query()->update(['ecommerce_connected' => true]);
    }

    /**
     * @throws Throwable
     */
    public function testBUserAccess(): void
    {
        $this->visit($this->uri)
            ->assertSee('Orders - Packed')
            ->assertSee('Orders - Active')
            ->assertSee('Active Orders By Age');
//        $this->basicUserAccessTest($this->uri, true);
//
//        $this->browse(function (Browser $browser) {
//            /** @var User $user */
//            $user = User::factory()->create();
//            $user->assignRole('user');
//
//            $browser->disableFitOnFailure();
//            $browser->loginAs($user);
//            $browser->visit($this->uri);
//            $browser->pause(300);
//            $browser->assertSee('Orders - Packed');
//            $browser->assertSee('Orders - Active');
//            $browser->assertSee('Active Orders By Age');
//        });
    }
}
