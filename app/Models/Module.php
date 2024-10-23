<?php

namespace App\Models;

use App\BaseModel;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * App\Module.
 *
 * @property int $id
 * @property string $service_provider_class
 * @property bool $enabled
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @mixin Eloquent
 */
class Module extends BaseModel
{
    protected $fillable = [
        'service_provider_class',
        'enabled',
        'name',
        'description',
    ];

    protected $appends = [
        'enabled' => false,
        'settings_link' => '',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
        ];
    }

    public function getNameAttribute()
    {
        return $this->service_provider_class::$module_name;
    }

    public function getDescriptionAttribute()
    {
        return $this->service_provider_class::$module_description;
    }

    public function getSettingsLinkAttribute()
    {
        return $this->service_provider_class::$settings_link;
    }
}
