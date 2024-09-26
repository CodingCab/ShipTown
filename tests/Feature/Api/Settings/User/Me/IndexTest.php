<?php

namespace Tests\Feature\Api\Settings\User\Me;

use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    public function testIndexCallReturnsOk(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->getJson(route('me.index'));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                ],
            ],
        ]);
    }
}
