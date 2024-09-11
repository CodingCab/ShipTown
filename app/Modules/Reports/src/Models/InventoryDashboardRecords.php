<?php

namespace App\Modules\Reports\src\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryDashboardRecords extends Model
{
    use SoftDeletes;

    protected $table = 'modules_reports_inventory_dashboard_records';

    protected $fillable = [
        'warehouse_id',
        'warehouse_code',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];
}
