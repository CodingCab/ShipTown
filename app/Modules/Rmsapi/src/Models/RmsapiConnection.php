<?php

namespace App\Modules\Rmsapi\src\Models;

use App\BaseModel;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;

/**
 * App\Models\RmsapiConnection.
 *
 * @property int $id
 * @property int $warehouse_id
 * @property string $location_id
 * @property string $url
 * @property string $username
 * @property string $password
 * @property int $products_last_timestamp
 * @property int $shippings_last_timestamp
 * @property int $sales_last_timestamp
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $price_field_name
 *
 * @method static Builder|RmsapiConnection newModelQuery()
 * @method static Builder|RmsapiConnection newQuery()
 * @method static Builder|RmsapiConnection query()
 * @method static Builder|RmsapiConnection whereCreatedAt($value)
 * @method static Builder|RmsapiConnection whereId($value)
 * @method static Builder|RmsapiConnection whereLocationId($value)
 * @method static Builder|RmsapiConnection wherePassword($value)
 * @method static Builder|RmsapiConnection whereProductsLastTimestamp($value)
 * @method static Builder|RmsapiConnection whereUpdatedAt($value)
 * @method static Builder|RmsapiConnection whereUrl($value)
 * @method static Builder|RmsapiConnection whereUsername($value)
 *
 * @mixin Eloquent
 */
class RmsapiConnection extends BaseModel
{
    use HasFactory;

    protected $table = 'modules_rmsapi_connections';

    protected $fillable = [
        'warehouse_id',
        'location_id',
        'url',
        'username',
        'password',
        'products_last_timestamp',
        'shippings_last_timestamp',
        'sales_last_timestamp',
        'price_field_name',
    ];

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Crypt::encryptString($password);
    }
}
