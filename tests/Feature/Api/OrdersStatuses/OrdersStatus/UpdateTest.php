<?php

namespace Tests\Feature\Api\OrdersStatuses\OrdersStatus;

use App\Models\OrderStatus;
use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    private function simulationTest()
    {
        $orderStatus = OrderStatus::create([
            'name' => 'testing',
            'code' => 'testing',
            'order_active' => 1,
            'reserves_stock' => 1,
            'sync_ecommerce' => 0,
        ]);
        $response = $this->put(route('api.orders-statuses.update', $orderStatus), [
            'order_active' => 0,
            'reserves_stock' => 0,
            'sync_ecommerce' => 0,
        ]);

        return $response;
    }

    public function testUpdateCallReturnsOk(): void
    {
        Passport::actingAs(
            User::factory()->admin()->create()
        );

        $response = $this->simulationTest();

        $response->assertSuccessful();
    }

    public function testUpdateCallShouldBeLoggedin(): void
    {
        $response = $this->simulationTest();

        $response->assertRedirect(route('login'));
    }

    public function testUpdateCallShouldLoggedinAsAdmin(): void
    {
        Passport::actingAs(
            User::factory()->create()
        );

        $response = $this->simulationTest();

        $response->assertForbidden();
    }
}
