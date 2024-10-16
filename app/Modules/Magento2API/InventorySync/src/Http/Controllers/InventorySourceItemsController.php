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
            ->addFilter(AllowedFilter::exact('product_sku', 'sku'));

        return $report->response();
    }
}
