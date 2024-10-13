<?php

namespace Tests\Jobs;

use App;
use App\Abstracts\JobTestAbstract;

class DispatchEveryDayEventJobTest extends JobTestAbstract
{
    public function test_job()
   {
        try {
            App\Jobs\DispatchEveryDayEventJob::dispatchSync();
        } catch (\Exception $e) {
            $this->fail('Job failed to run');
        }

        // $this->assertTrue(true, 'Job did not throw any exceptions');
    }
}
