<?php

namespace Tests\Feature\Api\Order\Shipments;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    public function testIndexCallReturnsOk(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->getJson(route('shipments.index'));

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                ],
            ],
        ]);
    }
}
