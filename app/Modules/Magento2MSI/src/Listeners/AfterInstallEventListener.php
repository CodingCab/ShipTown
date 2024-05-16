<?php

namespace App\Modules\Magento2MSI\src\Listeners;

use App\Models\ManualRequestJob;
use App\Modules\Magento2MSI\src\Jobs\RemoveProductsWithoutRequiredTagJob;

class AfterInstallEventListener
{
    public function handle(): void
    {
        ManualRequestJob::updateOrCreate([
            'job_class' => RemoveProductsWithoutRequiredTagJob::class,
        ], [
            'job_name' => 'Magento 2 MSI - Remove Products Without Required Tag',
        ]);
    }
}
