<?php

namespace App\Modules\Reports\src\Models;

use App\Models\InventoryReservation;
use Spatie\QueryBuilder\AllowedInclude;

class InventoryReservationReport extends Report
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->report_name = 'Inventory Reservations';
        $this->defaultSort = '-id';

        $this->baseQuery = InventoryReservation::query()
            ->leftJoin('products as product', 'inventory_reservations.product_sku', '=', 'product.sku')
            ->leftJoin('warehouses as warehouse', 'inventory_reservations.warehouse_code', '=', 'warehouse.code');

        $this->fields = [
            'id' => 'inventory_reservations.id',
            'product_sku' => 'product.sku',
            'product_name' => 'product.name',
            'warehouse_code' => 'warehouse.code',
            'warehouse_name' => 'warehouse.name',
            'quantity_reserved' => 'inventory_reservations.quantity_reserved',
            'comment' => 'inventory_reservations.comment',
            'created_at' => 'inventory_reservations.created_at',
        ];

        $this->casts = [
            'created_at' => 'datetime',
        ];

        $this->addAllowedInclude(AllowedInclude::relationship('causer'));
    }
}
