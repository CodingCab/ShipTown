<?php

namespace Tests\Unit\Modules\AutoStatusPicking\Jobs;

use App\Models\Pick;
use App\Modules\AutoStatusPicking\src\AutoStatusPickingServiceProvider;
use Tests\TestCase;

class UnDistributePickJobTest extends TestCase
{
    public function test_basic_functionality()
    {
        AutoStatusPickingServiceProvider::enableModule();

        $pick = Pick::factory()->create();

        $this->assertDatabaseHas('picks', ['is_distributed' => true, 'quantity_distributed' => $pick->quantity_picked]);

        $pick->delete();

        $this->assertDatabaseHas('picks', ['is_distributed' => true, 'quantity_distributed' => 0, 'deleted_at' => $pick->deleted_at]);
    }
}
