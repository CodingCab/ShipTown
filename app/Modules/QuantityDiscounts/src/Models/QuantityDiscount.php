<?php

namespace App\Modules\QuantityDiscounts\src\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuantityDiscount extends Model
{
    use SoftDeletes;

    protected $table = 'modules_quantity_discounts';

    protected $fillable = [
        'product_id',
        'quantity',
        'discount',
    ];
}
