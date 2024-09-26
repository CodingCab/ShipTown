<?php

namespace Tests\Feature\Api\Pdf\Print;

use App\User;
use Tests\TestCase;

class StoreTest extends TestCase
{
    public function testStoreReturnsUserPrinterIdMissingError(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->postJson('api/pdf/print', [
            'data' => [
                'labels' => ['label1', 'label2'],
            ],
            'template' => 'shelf-labels/6x4in-1-per-page',
        ]);

        $response->assertStatus(422);
    }

    public function testStoreReturnsAnOkResponse(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->postJson('api/pdf/print', [
            'data' => [
                'labels' => ['label1', 'label2'],
            ],
            'template' => 'shelf-labels/6x4in-1-per-page',
            'printer_id' => 1,
        ]);

        $this->assertDatabaseCount('modules_printnode_print_jobs', 1);

        $response->assertSuccessful();
    }
}
