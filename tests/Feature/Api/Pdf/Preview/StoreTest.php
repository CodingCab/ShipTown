<?php

namespace Tests\Feature\Api\Pdf\Preview;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    public function testStoreReturnsAnOkResponse(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->postJson('api/pdf/preview', [
            'data' => [
                'labels' => ['label1', 'label2'],
            ],
            'template' => 'shelf-labels/6x4in-1-per-page',
        ]);

        $response->assertOk();

    }
}
