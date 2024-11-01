<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportColumn extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_name',
        'expression',
        'alias',
        'type',
    ];
}
