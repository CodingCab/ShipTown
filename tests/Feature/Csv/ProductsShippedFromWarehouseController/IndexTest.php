<?php

namespace Tests\Feature\Csv\ProductsShippedFromWarehouseController;

use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Csv\ProductsShippedFromWarehouseController
 */
class IndexTest extends TestCase
{
    public function testIndexReturnsAnOkResponse(): void
    {
        $user = \App\User::factory()->create();

        $response = $this->actingAs($user)->get(route('warehouse_shipped.csv'));

        $response->assertOk();
    }
}
