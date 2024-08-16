<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        try {
            \Illuminate\Support\Facades\DB::unprepared('
                DROP TRIGGER trigger_on_products;
            ');
        } catch (\Exception $e) {
            //
        }

        \Illuminate\Support\Facades\DB::unprepared('
            CREATE TRIGGER trigger_on_products
            AFTER INSERT ON products
            FOR EACH ROW
            BEGIN
                INSERT INTO inventory (product_sku, product_id, warehouse_id, warehouse_code, created_at, updated_at)
                SELECT new.sku, new.id as product_id, warehouses.id as warehouse_id, warehouses.code as warehouse_code, now(), now() FROM warehouses;

                INSERT INTO products_aliases (product_id, alias, created_at, updated_at)
                VALUES (NEW.id, NEW.sku, now(), now())
                ON DUPLICATE KEY UPDATE product_id = NEW.id;
            END;
        ');
    }
};
