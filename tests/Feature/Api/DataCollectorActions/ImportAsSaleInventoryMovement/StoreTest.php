<?php

namespace Tests\Feature\Api\DataCollectorActions\ImportAsSaleInventoryMovement;

use App\Models\DataCollection;
use App\Models\DataCollectionRecord;
use App\Models\DataCollectionTransaction;
use App\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StoreTest extends TestCase
{
    private string $uri = 'api/data-collector-actions/import-as-sale-inventory-movement';

    /** @test */
    public function testIfCallReturnsOk(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('user', 'api'));

        /** @var DataCollection $dataCollection */
        $dataCollection = DataCollection::factory()->create([
            'type' => DataCollectionTransaction::class,
        ]);

        DataCollectionRecord::factory()->create([
            'data_collection_id' => $dataCollection->getKey(),
            'warehouse_id' => $dataCollection->warehouse_id,
            'warehouse_code' => $dataCollection->warehouse_code,
        ]);

        $response = $this->actingAs($user, 'api')->postJson($this->uri, [
            'data_collection_id' => $dataCollection->getKey(),
        ]);

        ray($response->json());

        $this->assertDatabaseHas('data_collections', [
            'id' => $dataCollection->getKey(),
            'custom_uuid' => null,
            'type' => DataCollectionTransaction::class,
        ]);

        $response->assertSuccessful();

        $response->assertJsonStructure([
            'data' => [
                'data_collection_id'
            ],
        ]);
    }
}
