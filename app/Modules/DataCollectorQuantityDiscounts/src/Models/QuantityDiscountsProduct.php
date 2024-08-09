<?php

namespace App\Modules\DataCollectorQuantityDiscounts\src\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

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
    use HasFactory;
    use SoftDeletes;

    protected $table = 'modules_quantity_discounts_products';

    protected $fillable = [
        'quantity_discount_id',
        'product_id',
    ];

    public static function getSpatieQueryBuilder(): QueryBuilder
    {
        return QueryBuilder::for(QuantityDiscountsProduct::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('quantity_discount_id'),
                AllowedFilter::exact('product_id'),
            ])
            ->allowedIncludes(['discount', 'product', 'product.prices']);
    }

    /**
     * @return HasOne
     */
    public function discount(): HasOne
    {
        return $this->hasOne(QuantityDiscount::class, 'id', 'quantity_discount_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
