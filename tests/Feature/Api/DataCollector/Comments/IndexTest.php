<?php

namespace Tests\Feature\Api\DataCollector\Comments;

use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    public function testIndexCallReturnsOk(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->get('api/data-collector/comments');

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                ],
            ],
        ]);
    }
}
