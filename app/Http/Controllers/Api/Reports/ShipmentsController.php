<?php

namespace App\Http\Controllers\Api\Reports;

use App\Modules\Reports\src\Models\OrderShipmentReport;

class ShipmentsController
{
    public function index()
    {
        return OrderShipmentReport::json();
    }
}
