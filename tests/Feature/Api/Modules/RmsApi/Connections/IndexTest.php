<?php

namespace Tests\Feature\Api\Modules\RmsApi\Connections;

use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    public function testIndexCallReturnsOk(): void
    {
        /** @var User $user * */
        $user = User::factory()->create();
        $user->assignRole('admin');

        $response = $this->actingAs($user, 'api')->getJson(route('api.modules.rmsapi.connections.index'));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                ],
            ],
        ]);
    }
}
