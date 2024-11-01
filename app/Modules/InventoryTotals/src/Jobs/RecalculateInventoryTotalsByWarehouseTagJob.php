<?php

namespace App\Modules\InventoryTotals\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Events\InventoryTotalsByWarehouseTagUpdatedEvent;
use App\Modules\InventoryTotals\src\Models\InventoryTotalByWarehouseTag;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecalculateInventoryTotalsByWarehouseTagJob extends UniqueJob
{
    public function handle(): void
    {
        InventoryTotalByWarehouseTag::where(['recalc_required' => true])
            ->chunkById(100, function (Collection $records) {
                $recordsUpdated = InventoryTotalByWarehouseTag::query()
                    ->whereIn('id', $records->pluck('id'))
                    ->update([
                        'recalc_required' => false,
                        'product_sku' => DB::raw('(
                            IFNULL(product_sku, (
                                    SELECT products.sku
                                    FROM products
                                    WHERE products.id = inventory_totals_by_warehouse_tag.product_id)
                                )
                        )'),
                        'warehouse_tag_name' => DB::raw('(
                            IFNULL(warehouse_tag_name, (
                                    SELECT taggables.tag_name
                                    FROM taggables
                                    WHERE taggables.tag_id = inventory_totals_by_warehouse_tag.tag_id
                                    LIMIT 1
                                )
                            )
                        )'),
                        'quantity' => DB::raw("(
                            SELECT SUM(inventory.quantity)
                            FROM inventory
                            INNER JOIN taggables
                                ON taggables.tag_id = inventory_totals_by_warehouse_tag.tag_id
                                AND taggables.taggable_type = 'App\\\\Models\\\\Warehouse'
                                AND taggables.taggable_id = inventory.warehouse_id
                            WHERE inventory.product_id = inventory_totals_by_warehouse_tag.product_id
                        )"),
                        'quantity_reserved' => DB::raw("(
                            SELECT SUM(inventory.quantity_reserved)
                            FROM inventory
                            INNER JOIN taggables
                                ON taggables.tag_id = inventory_totals_by_warehouse_tag.tag_id
                                AND taggables.taggable_type = 'App\\\\Models\\\\Warehouse'
                                AND taggables.taggable_id = inventory.warehouse_id

                            WHERE inventory.product_id = inventory_totals_by_warehouse_tag.product_id
                        )"),
                        'quantity_incoming' => DB::raw("(
                            SELECT SUM(inventory.quantity_incoming)
                            FROM inventory
                            INNER JOIN taggables
                                ON taggables.tag_id = inventory_totals_by_warehouse_tag.tag_id
                                AND taggables.taggable_type = 'App\\\\Models\\\\Warehouse'
                                AND taggables.taggable_id = inventory.warehouse_id
                            WHERE inventory.product_id = inventory_totals_by_warehouse_tag.product_id
                        )"),
                        'max_inventory_updated_at' => DB::raw("(
                            SELECT MAX(inventory.updated_at)
                            FROM inventory
                            INNER JOIN taggables
                                ON taggables.tag_id = inventory_totals_by_warehouse_tag.tag_id
                                AND taggables.taggable_type = 'App\\\\Models\\\\Warehouse'
                                AND taggables.taggable_id = inventory.warehouse_id
                            WHERE inventory.product_id = inventory_totals_by_warehouse_tag.product_id
                        )"),
                        'calculated_at' => now(),
                        'updated_at' => now(),
                    ]);

                $updatedInventoryTotalsByWarehouseTags = InventoryTotalByWarehouseTag::whereIn('id', $records->pluck('id'))->get();

                InventoryTotalsByWarehouseTagUpdatedEvent::dispatch($updatedInventoryTotalsByWarehouseTags);

                Log::debug('Job processing', ['job' => self::class, 'records_updated' => $recordsUpdated]);

                usleep(100000); // 0.1 sec
            });
    }
}
