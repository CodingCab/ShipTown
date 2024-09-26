<?php

namespace Tests\Feature\Api\Modules\RmsApi\Connections\Connection;

use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use App\User;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $admin = User::factory()->create()->assignRole('admin');
        $this->actingAs($admin, 'api');
    }

    public function testDestroyCallReturnsOk(): void
    {
        $rmsApi = RmsapiConnection::factory()->create();

        $response = $this->delete(route('api.modules.rmsapi.connections.destroy', $rmsApi));
        $response->assertStatus(200);
    }
}
