<?php

namespace Tests\Feature\Api\OrdersStatuses;

use App\Models\OrderStatus;
use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;

class StoreTest extends TestCase
{
    private function simulationTest()
    {
        $response = $this->post('api/orders-statuses', [
            'name' => 'Test Create',
            'code' => 'test-create',
            'order_active' => 0,
            'reserves_stock' => 0,
            'sync_ecommerce' => 0,
        ]);

        return $response;
    }

    public function testStoreCallReturnsOk(): void
    {
        Passport::actingAs(
            User::factory()->admin()->create()
        );

        $response = $this->simulationTest();

        $response->assertSuccessful();
    }

    public function testStoreCallShouldBeLoggedin(): void
    {
        $response = $this->simulationTest();

        $response->assertRedirect(route('login'));
    }

    public function testStoreCallShouldLoggedinAsAdmin(): void
    {
        Passport::actingAs(
            User::factory()->create()
        );

        $response = $this->simulationTest();

        $response->assertForbidden();
    }

    public function testCodeCannotDuplicate(): void
    {
        Passport::actingAs(
            User::factory()->admin()->create()
        );

        $this->simulationTest();
        $response = $this->simulationTest();

        $response->assertSessionHasErrors([
            'code',
        ]);
    }

    public function testAddDeletedStatusReturnOk(): void
    {
        Passport::actingAs(
            User::factory()->admin()->create()
        );

        $orderStatus = OrderStatus::create([
            'name' => 'testing',
            'code' => 'testing',
            'order_active' => 1,
            'reserves_stock' => 1,
            'sync_ecommerce' => 0,
        ]);

        $orderStatus->delete();

        $response = $this->post(route('api.orders-statuses.store'), [
            'name' => $orderStatus->name,
            'code' => $orderStatus->code,
            'order_active' => 0,
            'reserves_stock' => 0,
            'sync_ecommerce' => 0,
        ]);

        $response->assertStatus(200);
    }
}
