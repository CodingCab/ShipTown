<?php

namespace App\Modules\InventoryTotals\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Events\Inventory\RecalculateInventoryRequestEvent;
use App\Models\Inventory;
use Illuminate\Support\Facades\Log;

class RecalculateInventoryRecordsJob extends UniqueJob
{
    public function handle(): void
    {
        do {
            /** @var Inventory $inventory */
            $inventory = Inventory::where(['recount_required' => true])->limit(10)->get();

            Log::info($inventory);

            if ($inventory->isEmpty()) {
                return;
            }

            $inventoryRecordsIds = $inventory->pluck('id');

            if ($inventoryRecordsIds->isNotEmpty()) {
                RecalculateInventoryRequestEvent::dispatch($inventoryRecordsIds, $inventory);
            }

            $recordsUpdated = $inventory->update([
                'recount_required' => 0,
                'recalculated_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info('Job processing', [
                'job' => self::class,
                'recordsUpdated' => $recordsUpdated
            ]);

            usleep(50000); // 0.05 seconds
        } while ($recordsUpdated > 0);
    }
}
