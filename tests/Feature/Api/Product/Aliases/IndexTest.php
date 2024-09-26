<?php

namespace Tests\Feature\Api\Product\Aliases;

use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    public function testIndexCallReturnsOk(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->getJson(route('product.aliases.index'));

        $response->assertOk();
        $response->assertJsonStructure([
            'meta',
            'links',
            'data' => [
                '*' => [
                ],
            ],
        ]);
    }
}
