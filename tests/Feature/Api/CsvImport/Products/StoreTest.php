<?php

namespace Tests\Feature\Api\CsvImport\Products;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_store_call_returns_ok()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->postJson(route('api.csv-import.products.store'), [
            'data' => [
                [
                    'sku' => '55050',
                    'name' => 'Mediocre Leather Chair',
                    'department' => 'Toys',
                    'category' => 'Toys',
                    'price' => 43.84,
                    'sale_price' => 30.98,
                    'sale_price_start_date' => '2023-11-05',
                    'sale_price_end_date' => '2025-04-25',
                    'commodity_code' => '6109100010',
                    'supplier' => 'Linens SL',
                ],
            ],
        ]);

        ray($response->json());

        $response->assertSuccessful();

        $response->assertJsonStructure([
            'data' => [
                'success',
            ],
        ]);
    }
}
