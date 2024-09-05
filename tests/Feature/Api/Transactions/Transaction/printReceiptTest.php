<?php

namespace Feature\Api\Transactions\Transaction;

use App\Models\DataCollection;
use App\Models\OrderAddress;
use App\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class printReceiptTest extends TestCase
{
    private string $uri = '/api/transaction/receipt-print';

    /** @test */
    public function test_printReceipt_route()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('admin', 'api'));

        /** @var DataCollection $dataCollectionToUpdate */
        $dataCollectionToUpdate = DataCollection::factory()->create([
            'name' => 'Test Transaction',
            'type' => 'App\Models\DataCollectionTransaction',
        ]);

        $response = $this->actingAs($user, 'api')->postJson($this->uri, [
            'id' => $dataCollectionToUpdate->id,
            'printer_id' => 1,
            'epl' => true,
        ]);

        ray($response->json());

        $response->assertOk();

        $this->assertDatabaseHas(
            'data_collections',
            [
                'id' => $dataCollectionToUpdate->id,
                'name' => 'Test Transaction'
            ]
        );
    }
}
