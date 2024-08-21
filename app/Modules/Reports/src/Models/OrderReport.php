<?php

namespace App\Modules\Reports\src\Models;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

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
        $this->addField('total_shipping', 'orders.total_shipping', 'currency');
        $this->addField('total_discounts', 'orders.total_discounts', 'currency');
        $this->addField('total_outstanding', 'orders.total_outstanding', 'currency');
        $this->addField('status_code', 'orders.status_code');
        $this->addField('label_template', DB::raw('IF(orders.label_template, "Yes", "No")'));
        $this->addField('is_active', DB::raw('IF(orders.is_active, "Yes", "No")'));
        $this->addField('is_on_hold', DB::raw('IF(orders.is_on_hold, "Yes", "No")'));
        $this->addField('is_fully_paid', DB::raw('IF(orders.is_fully_paid, "Yes", "No")'));
        $this->addField('product_line_count', 'orders.product_line_count');
        $this->addField('total_products', 'orders.total_products');
        $this->addField('shipping_method_code', 'orders.shipping_method_code');
        $this->addField('shipping_method_name', 'orders.shipping_method_name');
        $this->addField('order_placed_at', 'orders.order_placed_at');
        $this->addField('picked_at', 'orders.picked_at', 'datetime');
        $this->addField('packed_at', 'orders.packed_at', 'datetime');
        $this->addField('order_closed_at', 'orders.order_closed_at', 'datetime');
    }
}
