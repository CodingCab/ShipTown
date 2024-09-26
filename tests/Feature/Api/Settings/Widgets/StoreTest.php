<?php

namespace Tests\Feature\Api\Settings\Widgets;

use App\User;
use Tests\TestCase;

class StoreTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $admin = User::factory()->create()->assignRole('admin');
        $this->actingAs($admin, 'api');
    }

    public function testStoreCallReturnsOk(): void
    {
        $response = $this->post(route('widgets.store'), [
            'name' => 'Tes widget',
            'config' => [],
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'name',
                'config' => [],
                'id',
            ],
        ]);
    }
}
