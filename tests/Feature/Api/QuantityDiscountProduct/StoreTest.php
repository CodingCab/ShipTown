<?php

namespace Tests\Feature\Admin\Settings\Modules\QuantityDiscounts;

use App\Modules\QuantityDiscounts\src\Models\QuantityDiscount;
use App\User;
use Tests\TestCase;

/**
 *
 */
class StoreTest extends TestCase
{
    /** @test */
    public function test_store_call_returns_ok()
    {
        $discount = QuantityDiscount::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->postJson(route('api.quantity-discounts.store', [
                'name' => $discount->getName(),
                'type' => $discount->getType(),
                'configuration' => $discount->getConfiguration(),
            ]));

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                'name',
                'type',
                'configuration'
            ],
        ]);
    }
}
