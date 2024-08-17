<?php

namespace App\Modules\Maintenance\src\Jobs;

use DB;

class FillProductSkuInDataCollectionRecordsTableJob
{
    public function handle(): void
    {
        do {
            $recordsAffected = DB::affectingStatement('
                WITH tempTable AS (
                    SELECT
                        data_collection_records.id,
                        products.sku as product_sku
                    FROM
                        data_collection_records
                        JOIN products ON data_collection_records.product_id = products.id

                    WHERE
                        data_collection_records.product_sku IS NULL

                    LIMIT 1000
                )

                UPDATE data_collection_records
                SET data_collection_records.product_sku = tempTable.product_sku
                INNER JOIN tempTable ON data_collection_records.id = tempTable.id
            ');

            usleep(100000); // 100ms
        } while ($recordsAffected > 0);
    }
}
