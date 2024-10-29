<?php

namespace App\Http\Controllers\Api\CsvImport;

use App\Helpers\TemporaryTable;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductsImportController extends Controller
{
    /**
     * @var string[][]
     */
    private array $rules = [
        'data' => ['required', 'array'],
        'data.*.sku' => ['required', 'string'],
        'data.*.name' => ['required', 'string'],
        'data.*.department' => ['nullable', 'string'],
        'data.*.category' => ['nullable', 'string'],
        'data.*.price' => ['nullable', 'numeric'],
        'data.*.sale_price' => ['nullable', 'numeric'],
        'data.*.sale_price_start_date' => ['nullable', 'date', 'date_format:Y-m-d'],
        'data.*.sale_price_end_date' => ['nullable', 'date', 'date_format:Y-m-d'],
        'data.*.commodity_code' => ['nullable', 'string'],
        'data.*.supplier' => ['nullable', 'string'],
    ];

    public function store(Request $request): JsonResource
    {
        $validatedData = Validator::make($request->all(), $this->rules)->validate();

        TemporaryTable::fromArray('tempTable_csv_products_import', $validatedData['data'], function (Blueprint $table) {
            $table->temporary();
            $table->id();
            $table->string('sku')->nullable();
            $table->string('name')->nullable();
            $table->string('department')->nullable();
            $table->string('category')->nullable();
            $table->decimal('price', 20, 3)->nullable();
            $table->decimal('sale_price', 20, 3)->nullable();
            $table->date('sale_price_end_date')->nullable();
            $table->date('sale_price_start_date')->nullable();
            $table->string('commodity_code')->nullable();
            $table->string('supplier')->nullable();
            $table->timestamps();
        });

        DB::statement('
            INSERT INTO products (
                sku,
                name,
                department,
                category,
                price,
                sale_price,
                sale_price_start_date,
                sale_price_end_date,
                commodity_code,
                supplier,
                created_at,
                updated_at
            )
            SELECT
                tempTable.sku,
                tempTable.name,
                tempTable.department,
                tempTable.category,
                tempTable.price,
                tempTable.sale_price,
                tempTable.sale_price_start_date,
                tempTable.sale_price_end_date,
                tempTable.commodity_code,
                tempTable.supplier,
                NOW(),
                NOW()
            FROM tempTable_csv_products_import as tempTable
            ON DUPLICATE KEY UPDATE
                name = VALUES(name),
                department = VALUES(department),
                category = VALUES(category),
                price = VALUES(price),
                sale_price = VALUES(sale_price),
                sale_price_start_date = VALUES(sale_price_start_date),
                sale_price_end_date = VALUES(sale_price_end_date),
                commodity_code = VALUES(commodity_code),
                supplier = VALUES(supplier),
                updated_at = NOW()
        ');

        foreach ($validatedData['data'] as $item) {
            $product = Product::where('sku', $item['sku'])->first();
            $tags = [$item['department'] ?? '', $item['category'] ?? '', $item['supplier'] ?? ''];

            ProductPrice::where('product_id', $product->id)->update([
                'price' => $item['price'],
                'sale_price' => $item['sale_price'],
                'sale_price_start_date' => $item['sale_price_start_date'],
                'sale_price_end_date' => $item['sale_price_end_date'],
                'is_on_sale' => $item['sale_price_end_date'] > now('Y-m-d'),
            ]);

            $product?->attachTags(array_filter($tags));
        }

        return JsonResource::make(['success' => true]);
    }
}
