<?php

namespace App\Modules\QuantityDiscounts\src\Models;

use App\Traits\LogsActivityTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class QuantityDiscount extends Model
{
    use LogsActivityTrait;
    use SoftDeletes;

    protected $table = 'modules_quantity_discounts';

    protected $fillable = [
        'name',
        'type',
        'configuration',
    ];

    protected $casts = [
        'configuration' => 'array',
    ];

    /**
     * @return QueryBuilder
     */
    public static function getSpatieQueryBuilder(): QueryBuilder
    {
        return QueryBuilder::for(QuantityDiscount::class)
            ->allowedFilters([
                AllowedFilter::scope('search', 'whereHasText')
            ])
            ->allowedSorts([
                'id',
                'name',
                'type'
            ])
            ->allowedIncludes([
                'products'
            ]);
    }

    /**
     * @param mixed $query
     * @param string $text
     *
     * @return mixed
     */
    public function scopeWhereHasText(mixed $query, string $text): mixed
    {
        return $query->where('name', $text)
            ->orWhere('type', $text)
            ->orWhere('name', 'like', '%'.$text.'%')
            ->orWhere('type', 'like', '%'.$text.'%');
    }
}
