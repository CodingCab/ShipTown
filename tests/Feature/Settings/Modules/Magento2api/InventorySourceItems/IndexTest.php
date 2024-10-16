<?php

namespace Tests\Feature\Settings\Modules\Magento2api\InventorySourceItems;

use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    private string $uri = '/settings/modules/magento2api/inventory-source-items';

    /** @test */
    public function testIfCallReturnsOk()
    {
        $user = User::factory()->create()->assignRole('admin');

        $response = $this->actingAs($user)->get($this->uri);

        $response->assertSuccessful();
    }
}
