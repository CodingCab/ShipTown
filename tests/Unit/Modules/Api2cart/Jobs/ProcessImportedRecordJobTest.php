<?php

namespace Tests\Unit\Modules\Api2cart\Jobs;

use App\Models\Order;
use App\Modules\Api2cart\src\Jobs\ProcessImportedOrdersJob;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Models\Api2cartOrderImports;
use Tests\TestCase;

class ProcessImportedRecordJobTest extends TestCase
{
    public function test()
    {
        Api2cartConnection::factory()->create();

        Api2cartOrderImports::factory()->create();

        ProcessImportedOrdersJob::dispatch();

        $order = Order::first();

        $this->assertNotEmpty($order, 'Order was not created');
    }
}
