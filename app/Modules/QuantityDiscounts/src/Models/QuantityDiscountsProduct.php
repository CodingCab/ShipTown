<?php

namespace App\Modules\QuantityDiscounts\src\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property integer id
 * @property integer quantity_discount_id
 * @property integer product_id
 * @property string created_at
 * @property string updated_at
 * @property string deleted_at
 *
 */
class QuantityDiscountsProduct extends Model
{
    use SoftDeletes;

    protected $table = 'modules_quantity_discounts_products';

    protected $fillable = [
        'quantity_discount_id',
        'product_id',
    ];
}
