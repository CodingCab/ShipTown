<?php

namespace Tests\Feature\Api\Reports\Shipments;

use App\Models\Order;

use App\Models\OrderShipment;
use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    /** @test */
    public function test_index_call_returns_ok()
    {
        $user = User::factory()->create();

        $order = Order::factory()->create();
        OrderShipment::factory()->create([
            'user_id' => $user->id,
            'order_id' => $order->id
        ]);

        $response = $this->actingAs($user, 'api')->getJson('/api/reports/shipments');

        $response->assertOk();
    }
}
