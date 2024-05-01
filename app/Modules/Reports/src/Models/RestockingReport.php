<?php

namespace App\Modules\Reports\src\Models;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\Tags\Tag;

class RestockingReport extends Report
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->report_name = 'Restocking Report';
        $this->view = 'reports.restocking-report';

        $this->defaultSort = '-quantity_required';
        $this->allowedIncludes = ['product', 'product.tags', 'product.prices', 'movementsStatistics'];

        $this->addField('inventory_id', 'inventory.id', hidden: false);
        $this->addField('product_id', 'inventory.product_id', hidden: false);
        $this->addField('warehouse_code', 'inventory.warehouse_code', hidden: false);
        $this->addField('product_sku', 'products.sku', hidden: false);
        $this->addField('product_name', 'products.name', hidden: false);
        $this->addField('quantity_required', 'inventory.quantity_required', hidden: false);
        $this->addField('quantity_in_stock', 'inventory.quantity', hidden: false);
        $this->addField('quantity_available', 'inventory.quantity_available', hidden: false);
        $this->addField('quantity_incoming', 'inventory.quantity_incoming', hidden: false);
        $this->addField('reorder_point', 'inventory.reorder_point', hidden: false);
        $this->addField('restock_level', 'inventory.restock_level', hidden: false);
        $this->addField('warehouse_quantity', DB::raw('IFNULL(inventory_source.quantity_available, 0)'), hidden: false);
        $this->addField('warehouse_has_stock', 'inventory_source.is_in_stock', hidden: false);
        $this->addField('first_sold_at', 'inventory.first_sold_at', 'datetime', hidden: false);
        $this->addField('last_sold_at', 'inventory.last_sold_at', 'datetime', hidden: false);
        $this->addField('last7days_sales_quantity_delta', DB::raw('IFNULL(inventory_movements_statistics.last7days_quantity_delta, 0)'), hidden: false);
        $this->addField('quantity_sold_last_7_days', DB::raw('IFNULL(inventory_movements_statistics.last7days_quantity_delta * -1, 0)'), hidden: false);
        $this->addField('first_received_at', 'inventory.first_received_at', 'datetime', hidden: false);
        $this->addField('last_received_at', 'inventory.last_received_at', 'datetime', hidden: false);
        $this->addField('last_movement_at', 'inventory.last_movement_at', 'datetime', hidden: false);
        $this->addField('last_counted_at', 'inventory.last_counted_at', 'datetime', hidden: false);
        $this->addField('inventory_source_shelf_location', 'inventory_source.shelve_location', hidden: false);

        $warehouseIds = Warehouse::withAnyTagsOfAnyType('fulfilment')->get('id');

        $this->baseQuery = Inventory::query()
            ->leftJoin('products', 'inventory.product_id', '=', 'products.id')
            ->leftJoin('inventory_movements_statistics', function (JoinClause $join) use ($warehouseIds) {
                $join->on('inventory.id', '=', 'inventory_movements_statistics.inventory_id');
                $join->where('inventory_movements_statistics.type', 'sale');
            })
            ->join('inventory as inventory_source', function (JoinClause $join) use ($warehouseIds) {
                $join->on('inventory_source.product_id', '=', 'inventory.product_id');
                $join->whereIn('inventory_source.warehouse_id', $warehouseIds);
            });

        $this->addFilter(
            AllowedFilter::callback('product_has_tags_containing', function ($query, $value) {
                $tags = Tag::containing($value)->get('id');

                $productsQuery = Product::withAnyTags($tags)->select('id');
                return $query->whereIn('inventory.product_id', $productsQuery);
            })
        );

        $this->addFilter(
            AllowedFilter::callback('product_has_tags', function ($query, $value) {
                $tags = Tag::findFromStringOfAnyType($value);

                $productsQuery = Product::withAnyTags($tags)->select('id');
                return $query->whereIn('inventory.product_id', $productsQuery);
            })
        );

        $this->addFilter(
            AllowedFilter::callback('search', function ($query, $value) {
                $query->where(function ($query) use ($value) {
                    $query->where('products.sku', 'like', '%'.$value.'%')
                        ->orWhere('products.name', 'like', '%'.$value.'%');
                });
            })
        );
    }
}
