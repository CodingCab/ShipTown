<?php

namespace Tests\Browser\Routes\Admin\Settings\Modules\QuantityDiscounts;

use App\Modules\QuantityDiscounts\src\Models\QuantityDiscount;
use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class IdPageTest extends DuskTestCase
{
    private string $uri = '/admin/settings/modules/quantity-discounts/{id}';

    /**
     * @throws Throwable
     */
    public function testBasics(): void
    {
        $this->basicAdminAccessTest($this->uri, true);
        $this->basicUserAccessTest($this->uri, false);
        $this->basicGuestAccessTest($this->uri);
    }

    /**
     * @throws Throwable
     */
    public function testPage(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('admin');

        $discount = QuantityDiscount::factory()->create();

        $this->browse(function (Browser $browser) use ($user, $discount) {
            $browser->disableFitOnFailure()
                ->loginAs($user)
                ->visit(str_replace('{id}', $discount->id, $this->uri))
                ->assertPathIs(str_replace('{id}', $discount->id, $this->uri))
                ->assertSourceMissing('Server Error');
        });
    }
}
