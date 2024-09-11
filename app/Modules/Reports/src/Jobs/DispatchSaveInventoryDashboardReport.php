<?php

namespace App\Modules\Reports\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Models\Warehouse;
use DB;

class DispatchSaveInventoryDashboardReport extends UniqueJob
{

    public function handle(): void
    {
        $fields = [
            'missing_restock_levels' => DB::raw('count(CASE WHEN inventory.restock_level <= 0 THEN 1 END)'),
            'wh_products_available' => DB::raw('count(*)'),
            'wh_products_out_of_stock' => DB::raw('count(CASE WHEN inventory.quantity_available = 0 AND inventory.restock_level > 0 THEN 1 END)'),
            'wh_products_required' => DB::raw('count(CASE WHEN inventory.quantity_required > 0 THEN 1 END)'),
            'wh_products_incoming' => DB::raw('count(CASE WHEN inventory.quantity_incoming > 0 THEN 1 END)'),
            'wh_products_stock_level_ok' => DB::raw('count(CASE ' .
                'WHEN (inventory.quantity_required = 0 AND inventory.restock_level > 0) ' .
                'THEN 1 ' .
                'END)'),
        ];

        $sourceWarehouses = Warehouse::withAnyTagsOfAnyType('fulfilment')->get();

        $sql = "
            INSERT INTO modules_reports_inventory_dashboard_records (warehouse_id, warehouse_code, data, created_at, updated_at)
            SELECT JSON_OBJECT(
                'warehouse_id', inventory.warehouse_id,
                'warehouse_code', inventory.warehouse_code,
                'missing_restock_levels', {$fields['missing_restock_levels']},
                'wh_products_available', {$fields['wh_products_available']},
                'wh_products_out_of_stock', {$fields['wh_products_out_of_stock']},
                'wh_products_required', {$fields['wh_products_required']},
                'wh_products_incoming', {$fields['wh_products_incoming']},
                'wh_products_stock_level_ok', {$fields['wh_products_stock_level_ok']}
            ) as data, NOW(), NOW()
            FROM inventory
            RIGHT JOIN inventory as inventory_source ON inventory_source.product_id = inventory.product_id
            AND inventory_source.warehouse_id IN (" . $sourceWarehouses->pluck('id')->implode(',') . ")
            AND inventory_source.quantity_available > 0
            LEFT JOIN products as product ON inventory.product_id = product.id
            WHERE inventory_source.quantity_available > 0
            GROUP BY inventory.warehouse_code, inventory.warehouse_id
            ON DUPLICATE KEY UPDATE 
                data = VALUES(data),
                updated_at = VALUES(updated_at)
        ";

        DB::statement($sql);
    }
}
