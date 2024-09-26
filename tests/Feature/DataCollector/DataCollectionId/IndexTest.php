<?php

namespace Tests\Feature\DataCollector\DataCollectionId;

use App\Models\DataCollection;
use App\Models\Warehouse;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    protected string $uri = 'data-collector';

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function testIfUriSet(): void
    {
        $this->assertNotEmpty($this->uri);
    }

    public function testGuestCall(): void
    {
        $response = $this->get($this->uri);

        $response->assertRedirect('/login');
    }

    public function testUserCall(): void
    {
        $this->actingAs($this->user, 'web');

        $dataCollection = DataCollection::factory()->create([
            'warehouse_id' => Warehouse::factory()->create()->getKey(),
            'name' => 'test',
        ]);

        $response = $this->get($this->uri.'/'.$dataCollection->id);

        $response->assertSuccessful();
    }

    public function testAdminCall(): void
    {
        $this->user->assignRole('admin');

        $this->actingAs($this->user, 'web');

        $dataCollection = DataCollection::factory()->create([
            'warehouse_id' => Warehouse::factory()->create()->getKey(),
            'name' => 'test',
        ]);

        $response = $this->get($this->uri.'/'.$dataCollection->id);

        $response->assertSuccessful();
    }
}
