<?php

namespace App\Modules\Automations\src\Conditions\Order;

use App\Modules\Automations\src\Abstracts\BaseOrderConditionAbstract;
use Illuminate\Database\Eloquent\Builder;

/**
 *
 */
class ShippingMethodNameEqualsCondition extends BaseOrderConditionAbstract
{
    /**
     * @param $expected_value
     */
    public static function addQueryScope(Builder $query, $expected_value): Builder
    {
        return $query->where('shipping_method_name', '=', $expected_value);
    }
}
