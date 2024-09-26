<?php

namespace Tests\Feature\Api\Modules\Printnode\Clients\Client;

use App\Modules\PrintNode\src\Models\Client;
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
        $client = Client::factory()->create();

        $response = $this->delete(route('api.modules.printnode.clients.destroy', $client));

        $response->assertSuccessful();
    }
}
