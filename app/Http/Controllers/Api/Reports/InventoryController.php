<?php

namespace App\Http\Controllers\Api\Reports;

use App\Modules\Reports\src\Models\InventoryReport;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryController
{
    public function index()
    {
        $data = (new InventoryReport())
            ->queryBuilder()
            ->offset(request('page') * request('per_page'))
            ->limit(request('per_page'))
            ->get()
        ;

        return JsonResource::collection($data);
    }
}
