<?php

use App\Models\DataCollection;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DataCollection::query()
            ->whereNull('warehouse_code')
            ->chunkById(5000, function ($records) {
                DataCollection::query()
                    ->whereIn('id', $records->pluck('id'))
                    ->update([
                        'warehouse_code' => \DB::raw('(SELECT warehouse_code FROM warehouses WHERE warehouses.id = warehouse_id)'),
                    ]);

                usleep(10000); // 10ms
            });

        try {
            Schema::table('data_collections', function (Blueprint $table) {
                $table->dropForeign('data_collections_warehouse_code_foreign');
            });
        } catch (\Exception $e) {
            //
        }

        Schema::table('data_collections', function (Blueprint $table) {
            $table->string('warehouse_code', 5)->nullable(false)->change();

            $table->foreign('warehouse_code')
                ->references('code')
                ->on('warehouses')
                ->cascadeOnDelete();
        });
    }
};
