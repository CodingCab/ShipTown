<?php

namespace App\Models;

use App\BaseModel;

/**
 * @property string $business_name
 * @property boolean $disable_2fa
 * @property string $database_version
 *
 */
class Configuration extends BaseModel
{
    protected $fillable = [
        'business_name',
        'database_version',
        'disable_2fa'
    ];
}
