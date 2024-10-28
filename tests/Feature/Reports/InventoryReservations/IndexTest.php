<?php

namespace Tests\Feature\Reports\InventoryReservations;

use App\Models\Inventory;
use App\Models\InventoryReservation;
use App\Models\Order;
use App\Models\Product;
use App\Models\Warehouse;
use App\User;
use Database\Seeders\InventorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 *
 */
class IndexTest extends TestCase
{
    /**
     * @var string
     */
    protected string $uri = '/reports/inventory-reservations';

    protected mixed $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function test_if_uri_set()
    {
        $this->assertNotEmpty($this->uri);
    }

    /** @test */
    public function test_guest_call()
    {
        $response = $this->get($this->uri);

        $response->assertRedirect('/login');
    }

    /** @test */
    public function test_user_call()
    {
        $this->actingAs($this->user, 'web');

        $this->createInventoryReservation();

        $response = $this->get($this->uri);

        $response->assertSuccessful();
    }

    /** @test */
    public function test_admin_call()
    {
        $this->user->assignRole('admin');

        $this->createInventoryReservation();

        $this->actingAs($this->user, 'web');

        $response = $this->get($this->uri);

        $response->assertSuccessful();
    }

    protected function createInventoryReservation(): void
    {

        $warehouse =Warehouse::factory()->create();
        $product = Product::factory()->create();

        $this->seed(InventorySeeder::class);
        $inventory = Inventory::where('warehouse_code', $warehouse->code)->first();
        $order = Order::factory()->create();

        InventoryReservation::create([
            'inventory_id' => $inventory->id,
            'product_sku' => $product->sku,
            'warehouse_code' => $warehouse->code,
            'quantity_reserved' => rand(1, 10),
            'comment' => 'Order #'.$order->order_number,
        ]);

    }
}
