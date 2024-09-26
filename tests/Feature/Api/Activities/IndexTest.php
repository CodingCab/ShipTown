<?php

namespace Tests\Feature\Api\Activities;

use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    public function testIndexCallReturnsOk(): void
    {
        $user = User::factory()->create()->assignRole('admin');

        $response = $this->actingAs($user, 'api')->getJson(route('api.activities.index'));

        $response->assertOk();
        $response->assertJsonStructure([
            'meta',
            'links',
            'data' => [
                '*' => [
                    'id',
                ],
            ],
        ]);
    }
}
