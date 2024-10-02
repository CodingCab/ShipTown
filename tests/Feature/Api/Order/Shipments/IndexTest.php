<?php

namespace Tests\Feature\Api\Order\Shipments;

use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    /** @test */
    public function test_index_call_returns_ok(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->getJson(route('api.orders.shipments.index'));

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                ],
            ],
        ]);
    }
}
