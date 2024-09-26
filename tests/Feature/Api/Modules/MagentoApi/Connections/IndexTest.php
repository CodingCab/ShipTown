<?php

namespace Tests\Feature\Api\Modules\MagentoApi\Connections;

use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    public function testIndexCallReturnsOk(): void
    {
        /** @var User $user * */
        $user = User::factory()->create();
        $user->assignRole('admin');

        $response = $this->actingAs($user, 'api')->getJson(route('api.modules.magento-api.connections.index'));

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                '*' => [],
            ],
        ]);
    }
}
