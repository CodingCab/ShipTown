<?php

namespace Tests\Feature\Api\QuantityDiscountProduct;

use App\Modules\QuantityDiscounts\src\Models\QuantityDiscount;
use App\User;
use Tests\TestCase;

/**
 *
 */
class StoreTest extends TestCase
{
    private string $uri = '/api/quantity-discount-product/';

    /** @test */
    public function testIfCallReturnsOk()
    {
        $discount = QuantityDiscount::factory()->create();
        $user = User::factory()->create()->assignRole('admin');

        $response = $this->actingAs($user, 'api')
            ->postJson(route($this->uri, [
                'name' => $discount->getName(),
                'type' => $discount->getType(),
                'configuration' => $discount->getConfiguration(),
            ]));

        ray($response->json());

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
