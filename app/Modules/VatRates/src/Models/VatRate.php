<?php

namespace App\Modules\VatRates\src\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * App\Models\PaymentType.
 *
 * @property int $id
 * @property string $code
 * @property string $rate
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class VatRate extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'code',
        'rate',
    ];

    protected $table = 'products_taxes';

    public static function getSpatieQueryBuilder(): QueryBuilder
    {
        return QueryBuilder::for(VatRate::class)
            ->allowedSorts([
                'id',
                'code',
                'rate',
            ]);
    }
}
