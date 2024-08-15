<?php

use App\Models\DataCollectionRecord;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $ids = DataCollectionRecord::query()
            ->whereNull('unit_cost')
            ->limit(5000)
            ->pluck('id');

        DataCollectionRecord::query()
            ->whereIn('id', $ids)
            ->update([
                'unit_cost' => 0,
                'unit_sold_price' => 0,
                'unit_full_price' => 0,
            ]);

        Schema::table('data_collection_records', function (Blueprint $table) {
            $table->decimal('unit_cost', 10, 3)->nullable(false)->change();
            $table->decimal('unit_sold_price', 10, 3)->nullable(false)->change();
            $table->decimal('unit_full_price', 10, 3)->nullable(false)->change();
        });
    }
};
