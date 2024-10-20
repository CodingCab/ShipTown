<?php

namespace App\Modules\Magento2API\InventorySync\src\Http\Controllers;

use App\Abstracts\ReportController;
use App\Modules\Magento2API\InventorySync\src\Models\Magento2msiProduct;
use App\Modules\Reports\src\Services\ReportService;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;

class InventorySourceItemsController extends ReportController
{
    public function index(Request $request): mixed
    {
        $query = Magento2msiProduct::query();

        $report = ReportService::fromQuery($query)
            ->addField('connection_id', 'connection_id')
            ->addField('magento_product_id', 'magento_product_id')
            ->addField('magento_product_type', 'magento_product_type')
            ->addField('inventory_totals_by_warehouse_tag_id', 'inventory_totals_by_warehouse_tag_id')
            ->addField('sync_required', 'sync_required')
            ->addField('custom_uuid', 'custom_uuid')
            ->addField('sku', 'sku')
            ->addField('source_code', 'source_code')
            ->addField('quantity', 'quantity')
            ->addField('status', 'status')
            ->addField('inventory_source_items_fetched_at', 'inventory_source_items_fetched_at')
            ->addField('inventory_source_items', 'inventory_source_items')
            ->addField('created_at', 'created_at')
            ->addField('updated_at', 'updated_at')
            ->addFilter(AllowedFilter::exact('sku', 'sku'))
            ->addFilter(AllowedFilter::exact('product_sku', 'sku'));

        return $report->response();
    }
}
