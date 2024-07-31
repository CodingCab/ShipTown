<?php

namespace App\Modules\QuantityDiscounts\src;

use App\Events\DataCollectionRecord\DataCollectionRecordCreatedEvent;
use App\Events\DataCollectionRecord\DataCollectionRecordDeletedEvent;
use App\Events\DataCollectionRecord\DataCollectionRecordUpdatedEvent;
use App\Modules\BaseModuleServiceProvider;

class QuantityDiscountsServiceProvider extends BaseModuleServiceProvider
{
    public static string $module_name = 'eCommerce - Quantity Discounts';

    public static string $module_description = 'Module provides an ability to create quantity discounts for products';

    public static string $settings_link = '/admin/settings/modules/quantity-discounts';

    public static bool $autoEnable = true;

    protected $listen = [
        DataCollectionRecordCreatedEvent::class => [
            Listeners\DataCollectionRecordCreatedEventListener::class,
        ],

        DataCollectionRecordUpdatedEvent::class => [
            Listeners\DataCollectionRecordUpdatedEventListener::class,
        ],

        DataCollectionRecordDeletedEvent::class => [
            Listeners\DataCollectionRecordDeletedEventListener::class,
        ],
    ];

    public static function enabling(): bool
    {
        return parent::enabling();
    }
}
