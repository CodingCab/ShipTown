<?php

namespace App\Http\Controllers\Api\Reports;

use App\Modules\Reports\src\Models\PicksReport;
use Illuminate\Http\Resources\Json\JsonResource;

class PicksController
{
    public function index()
    {
        $data = (new PicksReport())
            ->queryBuilder()
            ->offset(request('page') * request('per_page'))
            ->limit(request('per_page'))
            ->get()
        ;

        return JsonResource::collection($data);
    }
}
