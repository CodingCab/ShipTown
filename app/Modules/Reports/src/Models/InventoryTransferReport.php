<?php

namespace App\Modules\Reports\src\Models;

use App\Models\DataCollectionRecord;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;

class InventoryTransferReport extends Report
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->report_name = 'Inventory Transfers';

        $this->baseQuery = DataCollectionRecord::query()
            ->leftJoin('data_collections', 'data_collections.id', '=', 'data_collection_records.data_collection_id')
            ->leftJoin('products', 'products.id', '=', 'data_collection_records.product_id')
            ->leftJoin('tags as department_tag', function ($join) {
                $join->where('department_tag.type', '=', 'rms_department_name')
                    ->whereIn('department_tag.id', function ($query) {
                        $query->select('tag_id')
                            ->from('taggables')
                            ->where('taggables.taggable_id', '=', DB::raw('data_collection_records.product_id'))
                            ->where('taggables.taggable_type', '=', 'App\\Models\\Product');
                    });
            })
            ->leftJoin('tags as category_tag', function ($join) {
                $join->where('category_tag.type', '=', 'rms_category_name')
                    ->whereIn('category_tag.id', function ($query) {
                        $query->select('tag_id')
                            ->from('taggables')
                            ->where('taggables.taggable_id', '=', DB::raw('data_collection_records.product_id'))
                            ->where('taggables.taggable_type', '=', 'App\\Models\\Product');
                    });
            })
            ->leftJoin('products_prices', function ($join) {
                $join->on('products_prices.product_id', '=', 'data_collection_records.product_id')
                    ->whereColumn('products_prices.warehouse_id', 'data_collections.warehouse_id');
            });

        $this->allowedIncludes = [
            'product',
            'product.tags',
            'product.aliases',
            'dataCollection',
            'inventory',
            'products_prices',
        ];

        $this->defaultSelects = [
            'warehouse_id',
            'department',
            'category',
            'transfer_name',
            'product_sku',
            'product_name',
            'product_cost' ,
            'total_transferred_in',
            'updated_at'
        ];

        $this->fields = [
            'warehouse_id'          => 'data_collections.warehouse_id',
            'department'            => 'department_tag.name',
            'category'              => 'category_tag.name',
            'transfer_name'         => 'data_collections.name',
            'product_sku'           => 'products.sku',
            'product_name'          => 'products.name',
            'product_cost'          => 'products_prices.cost',
            'total_transferred_in'  => 'data_collection_records.total_transferred_in',
            'updated_at'            => 'data_collection_records.updated_at',
        ];

        $this->casts = [
            'warehouse_id'          => 'integer',
            'department'            => 'string',
            'category'              => 'string',
            'transfer_name'         => 'string',
            'product_sku'           => 'string',
            'product_name'          => 'string',
            'product_cost'          => 'float',
            'total_transferred_in'  => 'float',
            'updated_at'            => 'datetime',
        ];

        $this->addFilter(
            AllowedFilter::callback('has_tags', function ($query, $value) {
                $query->whereHas('product', function ($query) use ($value) {
                    $query->withAllTags($value);
                });
            })
        );

        $this->addFilter(
            AllowedFilter::scope('sku_or_alias', 'skuOrAlias'),
        );
    }
}
