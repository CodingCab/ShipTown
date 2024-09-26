<?php

namespace Tests\Unit\Modules\QueueMonitor;

use App\Modules\QueueMonitor\src\Jobs\SampleJob;
use App\Modules\QueueMonitor\src\QueueMonitorServiceProvider;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    public function testModuleBasicFunctionality(): void
    {
        QueueMonitorServiceProvider::enableModule();

        SampleJob::dispatch();

        $this->assertTrue(true, 'Jobs dispatched without errors');
    }
}
