<?php

namespace Tests\Jobs;

use App;
use App\Abstracts\JobTestAbstract;

class DispatchEveryDayEventJobTest extends JobTestAbstract
{
    public function test_job()
    {
        App\Jobs\DispatchEveryDayEventJob::dispatchSync();
    }
}
