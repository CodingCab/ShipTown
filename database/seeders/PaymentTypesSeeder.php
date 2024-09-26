<?php

namespace Database\Seeders;

use App\Models\PaymentType;
use Illuminate\Database\Seeder;

class PaymentTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentTypes = [
            [
                'payment_code' => 'CASH',
                'payment_name' => 'Cash',
            ],
            [
                'payment_code' => 'CREDIT_CARD',
                'payment_name' => 'Credit Card',
            ],
            [
                'payment_code' => 'DEBIT_CARD',
                'payment_name' => 'Debit Card',
            ],
            [
                'payment_code' => 'BANK_TRANSFER',
                'payment_name' => 'Bank Transfer',
            ],
        ];

        foreach ($paymentTypes as $paymentType) {
            PaymentType::create($paymentType);
        }
    }
}
