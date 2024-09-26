<?php

namespace Tests\Feature\Api\ShippingServices;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    public function testIndexCallReturnsOk(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->getJson(route('api.shipping-services.index'));

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
