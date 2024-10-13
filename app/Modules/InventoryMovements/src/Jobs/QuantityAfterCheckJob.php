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

                LIKE 100;
            ', [$this->date->startOfDay()->toDateTimeLocalString(), $this->date->endOfDay()->toDateTimeLocalString()]);

            $recordsUpdated = DB::update('
                UPDATE inventory_movements

                INNER JOIN tempTable
                    ON tempTable.inventory_movement_id = inventory_movements.id

                SET
                    inventory_movements.re = quantity_before + quantity_delta,
                    inventory_movements.updated_at = NOW()
            ');

            DB::update('
                UPDATE inventory

                INNER JOIN tempTable
                    ON tempTable.inventory_id = inventory.id

                SET
                    inventory_movements.recount_required = 1,
                    inventory_movements.updated_at = NOW()
            ');

            Log::info('Job processing', [
                'job' => self::class,
                'recordsUpdated' => $recordsUpdated,
            ]);

            usleep(100000); // 0.1 seconds
        } while ($recordsUpdated > 0 && $maxRounds > 0);
    }
}
