<?php

namespace App\Modules\OrderTotals\src\Services;

use App\Models\Order;
use App\Models\OrderProductTotal;
use App\Modules\OrderTotals\src\Jobs\EnsureAllRecordsExistsJob;
use App\Modules\OrderTotals\src\Jobs\EnsureCorrectTotalsJob;
use Illuminate\Support\Facades\DB;

class OrderTotalsService
{
    public static function dispatchAllJobs(): void
    {
        EnsureAllRecordsExistsJob::dispatch();
        EnsureCorrectTotalsJob::dispatch();
    }

    public static function updateTotals(int $order_id)
    {
        $record = DB::table('orders_products')
            ->selectRaw('
                order_id,
                count(id) as count_expected,
                sum(quantity_ordered) as quantity_ordered_expected,
                sum(quantity_split) as quantity_split_expected,
                sum(total_price) as total_price_expected,
                sum(quantity_picked) as quantity_picked_expected,
                sum(quantity_skipped_picking) as quantity_skipped_picking_expected,
                sum(quantity_not_picked) as quantity_not_picked_expected,
                sum(quantity_shipped) as quantity_shipped_expected,
                sum(quantity_to_pick) as quantity_to_pick_expected,
                sum(quantity_to_ship) as quantity_to_ship_expected,
                max(updated_at) as max_updated_at_expected
            ')
            ->where(['order_id' => $order_id])
            ->whereNull('deleted_at')
            ->groupBy('order_id')
            ->get()
            ->first();

        if (empty($record)) {
            OrderProductTotal::query()->create(['order_id' => $order_id]);

            return;
        }

        $data = [
            'count' => data_get($record, 'count_expected', 0),
            'quantity_ordered' => data_get($record, 'quantity_ordered_expected', 0),
            'quantity_split' => data_get($record, 'quantity_split_expected', 0),
            'total_price' => data_get($record, 'total_price_expected', 0),
            'quantity_picked' => data_get($record, 'quantity_picked_expected', 0),
            'quantity_skipped_picking' => data_get($record, 'quantity_skipped_picking_expected', 0),
            'quantity_not_picked' => data_get($record, 'quantity_not_picked_expected', 0),
            'quantity_shipped' => data_get($record, 'quantity_shipped_expected', 0),
            'quantity_to_pick' => data_get($record, 'quantity_to_pick_expected', 0),
            'quantity_to_ship' => data_get($record, 'quantity_to_ship_expected', 0),
            'max_updated_at' => data_get($record, 'max_updated_at_expected', now()),
        ];

        OrderProductTotal::query()
            ->updateOrCreate(['order_id' => $order_id], $data);

        Order::query()
            ->where(['id' => $order_id])
            ->update([
                'product_line_count' => data_get($record, 'count_expected', 0),
                'total_products' => data_get($record, 'total_price_expected'),
                'updated_at' => now(),
            ]);
    }
}
