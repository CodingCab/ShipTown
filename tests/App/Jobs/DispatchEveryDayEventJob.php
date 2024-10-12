<?php

namespace Tests\App\Jobs;

use App;
use App\Abstracts\JobTestAbstract;

class DispatchEveryDayEventJob extends JobTestAbstract
{
    public function test_job()
    {
        App\Jobs\DispatchEveryDayEventJob::dispatchSync();

        $this->assertTrue(true, 'Job test passed');
    }
}
