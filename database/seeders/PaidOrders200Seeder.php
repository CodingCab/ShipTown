<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Database\Seeder;

class PaidOrders200Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Order::factory()
            ->count(20)
            ->create(['status_code' => 'new', 'label_template' => 'address_label', 'shipping_address_id' => $this->createIrishShippingAddress()->getKey()])
            ->each(function (Order $order) {
                Product::query()
                    ->inRandomOrder()
                    ->get()
                    ->each(function (Product $product) use ($order) {
                        return OrderProduct::create([
                            'order_id' => $order->getKey(),
                            'product_id' => $product->getKey(),
                            'quantity_ordered' => rand(1, 2),
                            'price' => $product->price,
                            'name_ordered' => $product->name,
                            'sku_ordered' => $product->sku,
                        ]);
                    });

                $order = $order->refresh();
                Order::query()->where(['id' => $order->getKey()])->update(['total_paid' => $order->total_order]);
            });

        Order::factory()
            ->count(30)
            ->create(['status_code' => 'new', 'label_template' => 'address_label', 'shipping_address_id' => $this->createIrishShippingAddress()->getKey()])
            ->each(function (Order $order) {
                Product::query()
                    ->inRandomOrder()
                    ->limit(1)
                    ->get()
                    ->each(function (Product $product) use ($order) {
                        return OrderProduct::create([
                            'order_id' => $order->getKey(),
                            'product_id' => $product->getKey(),
                            'quantity_ordered' => rand(1, 2),
                            'price' => $product->price,
                            'name_ordered' => $product->name,
                            'sku_ordered' => $product->sku,
                        ]);
                    });

                $order = $order->refresh();
                Order::query()->where(['id' => $order->getKey()])->update(['total_paid' => $order->total_order]);
            });

        Order::factory()
            ->count(70)
            ->create(['status_code' => 'new', 'label_template' => 'address_label', 'shipping_address_id' => $this->createIrishShippingAddress()->getKey()])
            ->each(function (Order $order) {
                Product::query()
                    ->inRandomOrder()
                    ->limit(2)
                    ->get()
                    ->each(function (Product $product) use ($order) {
                        return OrderProduct::create([
                            'order_id' => $order->getKey(),
                            'product_id' => $product->getKey(),
                            'quantity_ordered' => rand(1, 2),
                            'price' => $product->price,
                            'name_ordered' => $product->name,
                            'sku_ordered' => $product->sku,
                        ]);
                    });

                $order = $order->refresh();
                Order::query()->where(['id' => $order->getKey()])->update(['total_paid' => $order->total_order]);
            });

        Order::factory()
            ->count(40)
            ->create(['status_code' => 'new', 'label_template' => 'address_label', 'shipping_address_id' => $this->createIrishShippingAddress()->getKey()])
            ->each(function (Order $order) {
                Product::query()
                    ->inRandomOrder()
                    ->limit(3)
                    ->get()
                    ->each(function (Product $product) use ($order) {
                        return OrderProduct::create([
                            'order_id' => $order->getKey(),
                            'product_id' => $product->getKey(),
                            'quantity_ordered' => rand(1, 2),
                            'price' => $product->price,
                            'name_ordered' => $product->name,
                            'sku_ordered' => $product->sku,
                        ]);
                    });

                $order = $order->refresh();
                Order::query()->where(['id' => $order->getKey()])->update(['total_paid' => $order->total_order]);
            });

        Order::factory()
            ->count(20)
            ->create(['status_code' => 'new', 'shipping_address_id' => $this->createIrishShippingAddress()->getKey()])
            ->each(function (Order $order) {
                Product::query()
                    ->inRandomOrder()
                    ->limit(4)
                    ->get()
                    ->each(function (Product $product) use ($order) {
                        return OrderProduct::create([
                            'order_id' => $order->getKey(),
                            'product_id' => $product->getKey(),
                            'quantity_ordered' => rand(1, 2),
                            'price' => $product->price,
                            'name_ordered' => $product->name,
                            'sku_ordered' => $product->sku,
                        ]);
                    });

                $order = $order->refresh();
                Order::query()->where(['id' => $order->getKey()])->update(['total_paid' => $order->total_order]);
            });

        Order::factory()
            ->count(10)
            ->create(['status_code' => 'new', 'label_template' => 'address_label', 'shipping_address_id' => $this->createIrishShippingAddress()->getKey()])
            ->each(function (Order $order) {
                Product::query()
                    ->inRandomOrder()
                    ->get()
                    ->each(function (Product $product) use ($order) {
                        return OrderProduct::create([
                            'order_id' => $order->getKey(),
                            'product_id' => $product->getKey(),
                            'quantity_ordered' => rand(1, 2),
                            'price' => $product->price,
                            'name_ordered' => $product->name,
                            'sku_ordered' => $product->sku,
                        ]);
                    });

                $order = $order->refresh();
                Order::query()->where(['id' => $order->getKey()])->update(['total_paid' => $order->total_order]);
            });
    }

    /**
     * @return mixed
     */
    public function createIrishShippingAddress(): mixed
    {
        return OrderAddress::factory()->create(['country_name' => 'Ireland', 'country_code' => 'IE']);
    }
}