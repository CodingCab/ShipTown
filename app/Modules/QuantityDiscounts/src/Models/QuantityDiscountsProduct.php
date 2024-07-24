<?php

namespace App\Modules\QuantityDiscounts\src\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuantityDiscountsProduct extends Model
{
    use SoftDeletes;

    protected $table = 'modules_quantity_discounts_products';

    protected $fillable = [
        'quantity_discount_id',
        'product_id',
    ];

    /**
     * @return HasOne
     */
    public function discount(): HasOne
    {
        return $this->hasOne(QuantityDiscount::class, 'id', 'quantity_discount_id');
    }
}
