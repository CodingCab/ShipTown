<?php

namespace App\Http\Controllers\Reports;

use App\Abstracts\ReportController;
use App\Modules\Reports\src\Models\OrderReport;
use Illuminate\Http\Request;

class OrderController extends ReportController
{
    public function index(Request $request): mixed
    {
        $report = new OrderReport();

        return $report->response($request);
    }
}
