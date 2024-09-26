<?php

namespace Tests\Feature\Api\DataCollector\DataCollector;

use App\Models\DataCollection;
use App\User;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    public function testDestroyCallReturnsOk(): void
    {
        $user = User::factory()->create();

        $dataCollector = DataCollection::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->delete(route('api.data-collector.destroy', $dataCollector->getKey()));

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                'id',
            ],
        ]);
    }
}
