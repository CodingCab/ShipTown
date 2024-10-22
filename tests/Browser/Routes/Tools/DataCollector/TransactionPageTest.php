<?php

namespace Tests\Browser\Routes\Tools\DataCollector;

use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Warehouse;
use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class TransactionPageTest extends DuskTestCase
{
    private string $uri = '/tools/data-collector/transaction';

    /**
     * @throws Throwable
     */
    public function testPage(): void
    {
        /** @var Warehouse $warehouse */
        $warehouse = Warehouse::factory()->create();

        Product::factory()->create(['sku' => '4001']);

        ProductPrice::query()->update(['price' => 10]);

        /** @var User $user */
        $user = User::factory()->create([
            'warehouse_code' => $warehouse->code,
            'warehouse_id' => $warehouse->getKey(),
        ]);

        $user->assignRole('admin');

        $this->browse(function (Browser $browser) use ($user) {
            $browser->disableFitOnFailure();
            $browser->loginAs($user)
                ->visit($this->uri)
                ->assertPathIs($this->uri)
                ->pause(2000)
                ->keys('@barcode-input-field', '4001')
                ->keys('@barcode-input-field', '{ENTER}')
                ->pause($this->shortDelay)
                ->keys('@barcode-input-field', '4001')
                ->keys('@barcode-input-field', '{ENTER}')
                ->pause($this->shortDelay)
                ->keys('@barcode-input-field', '4001')
                ->keys('@barcode-input-field', '{ENTER}')
                ->pause($this->shortDelay)
                ->click('#options-button')
                ->pause($this->shortDelay)
                ->click('#choose-payment-type')
                ->pause($this->shortDelay)
                ->click('#data-collection-choose-payment-type-modal [data-code="CASH"] button')
                ->pause($this->shortDelay)
                ->click('#data-collection-choose-payment-type-modal button[data-choose-payment]')
                ->pause($this->shortDelay)
                ->value('#transaction_payment_amount', '100')
                ->pause($this->shortDelay)
                ->click('#data-collection-add-payment-modal button[data-save-amount]')
                ->pause($this->shortDelay)
                ->click('#configuration-modal footer .btn-primary')
                ->pause($this->shortDelay)
                ->assertSourceMissing('Server Error');
        });
    }

    /**
     * @throws Throwable
     */
    public function testUserAccess(): void
    {
        $this->basicUserAccessTest($this->uri, true);
    }
}
