<?php

namespace App\Modules\InventoryMovements\src\Jobs;

use App\Abstracts\UniqueJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuantityAfterCheckJob extends UniqueJob
{
    private Carbon $date;

    public function __construct($date = null)
    {
        $this->date = $date ?? Carbon::now();
    }

    public function handle(): void
    {
        $maxRounds = 500;

        do {
            $maxRounds--;
            DB::statement('DROP TEMPORARY TABLE IF EXISTS tempTable');

            DB::statement('
                CREATE TEMPORARY TABLE tempTable AS
                SELECT
                    id as inventory_movement_id,
                    inventory_id
                FROM inventory_movements
                WHERE
                    type != "stocktake"
                    AND quantity_after != quantity_before + quantity_delta
                    AND updated_at BETWEEN ? AND ?
            ', [$this->date->startOfDay()->toDateTimeString(), $this->date->endOfDay()->toDateTimeString()]);

            $recordsUpdated = DB::update('
                UPDATE inventory_movements
                INNER JOIN tempTable
                    ON tempTable.inventory_movement_id = inventory_movements.id
                SET
                    inventory_movements.quantity_after = inventory_movements.quantity_before + inventory_movements.quantity_delta,
                    inventory_movements.updated_at = NOW()
            ');

            DB::update('
                UPDATE inventory
                INNER JOIN tempTable
                    ON tempTable.inventory_id = inventory.id
                SET
                    inventory.recount_required = 1,
                    inventory.updated_at = NOW()
            ');

            Log::info('Job processing', [
                'job' => self::class,
                'recordsUpdated' => $recordsUpdated,
            ]);

            usleep(100000); // 0.1 seconds
        } while ($recordsUpdated > 0 && $maxRounds > 0);
    }
}
