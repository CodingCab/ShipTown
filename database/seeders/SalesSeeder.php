<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Modules\InventoryMovements\src\Jobs\SequenceNumberJob;
use App\Modules\InventoryMovementsStatistics\src\Jobs\RecalculateStatisticsTableJob;
use Illuminate\Database\Seeder;

class SalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $inventoryMovements = Inventory::query()
            ->where('quantity', '>', 0)
            ->inRandomOrder()
            ->get()
            ->map(function (Inventory $inventory) {
                $quantityDelta = -rand(1, $inventory->quantity / 2);

                return [
                    'occurred_at' => now(),
                    'type' => 'sale',
                    'inventory_id' => $inventory->id,
                    'product_id' => $inventory->product_id,
                    'warehouse_code' => $inventory->warehouse_code,
                    'warehouse_id' => $inventory->warehouse_id,
                    'quantity_before' => $inventory->quantity,
                    'quantity_delta' => $quantityDelta,
                    'quantity_after' => $inventory->quantity + $quantityDelta,
                    'description' => 'sale',
                ];
            });

        InventoryMovement::query()->insert($inventoryMovements->toArray());

        SequenceNumberJob::dispatch();

        RecalculateStatisticsTableJob::dispatch();
    }
}
