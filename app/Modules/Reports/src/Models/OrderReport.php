<?php

namespace App\Modules\Reports\src\Models;

use App\Models\Order;

class OrderReport extends Report
{
    public string $view = 'order';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->report_name = 'Orders';

        $this->baseQuery = Order::query()
            ->leftJoin('orders_addresses as order_address', 'orders.shipping_address_id', '=', 'order_address.id');

        $this->addField('order_number', 'orders.order_number');
        $this->addField('order_created_at', 'orders.created_at', 'datetime');
        $this->addField('company', 'order_address.company');
        $this->addField('address1', 'order_address.address1');
        $this->addField('address2', 'order_address.address1');
        $this->addField('city', 'order_address.city');
        $this->addField('state_name', 'order_address.state_name');
        $this->addField('country_name', 'order_address.country_name');
        $this->addField('total_order', 'orders.total_order', 'currency');
        $this->addField('total_paid', 'orders.total_paid', 'currency');
    }
}
