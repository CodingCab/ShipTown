<?php

namespace Tests\Jobs;

use App\Abstracts\JobTestAbstract;

class DispatchEveryMonthEventJobTest extends JobTestAbstract
{
    public function test_job()
    {
        $job = self::createJob();

        $job::dispatch();
    }
}