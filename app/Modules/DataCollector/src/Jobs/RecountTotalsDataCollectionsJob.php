<?php

namespace App\Modules\DataCollector\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Models\DataCollection;
use Illuminate\Support\Facades\DB;

class RecountTotalsDataCollectionsJob extends UniqueJob
{
    public function handle(): void
    {
        DB::statement("DROP TEMPORARY TABLE IF EXISTS tempTable;");
        DB::statement("DROP TEMPORARY TABLE IF EXISTS tempInventoryTotals;");

        DB::statement("
            CREATE TEMPORARY TABLE tempTable AS
                SELECT
                    data_collections.id as data_collection_id,
                    NOW() as calculated_at
                FROM data_collections

                WHERE recount_required = 1

                LIMIT 5;
        ");

        DB::statement("
            CREATE TEMPORARY TABLE tempInventoryTotals AS
                SELECT
                     tempTable.data_collection_id as data_collection_id,
                     SUM(data_collection_records.quantity_scanned) as total_quantity_scanned,
                     SUM(data_collection_records.total_cost) as total_cost,
                     SUM(data_collection_records.total_sold_price) as total_sold_price,
                     SUM(data_collection_records.total_full_price) as total_full_price,
                     SUM(data_collection_records.total_discount) as total_discount,
                     SUM(data_collection_records.total_profit) as total_profit,

                     tempTable.calculated_at as calculated_at,
                     NOW() as created_at,
                     NOW() as updated_at

                FROM tempTable

                LEFT JOIN data_collection_records
                    ON data_collection_records.data_collection_id = tempTable.data_collection_id

                GROUP BY tempTable.data_collection_id, tempTable.calculated_at;
        ");

        DB::update("
            UPDATE data_collections

            INNER JOIN tempInventoryTotals
                ON data_collections.id = tempInventoryTotals.data_collection_id

            SET
                data_collections.recount_required = 0,
                data_collections.total_quantity_scanned = tempInventoryTotals.total_quantity_scanned,
                data_collections.total_cost = tempInventoryTotals.total_cost,
                data_collections.total_sold_price = tempInventoryTotals.total_sold_price,
                data_collections.total_full_price = tempInventoryTotals.total_full_price,
                data_collections.total_discount = tempInventoryTotals.total_discount,
                data_collections.total_profit = tempInventoryTotals.total_profit,
                data_collections.calculated_at = tempInventoryTotals.calculated_at,
                data_collections.updated_at = NOW();
        ");
    }
}
