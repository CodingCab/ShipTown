<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders_billing_addresses', function (Blueprint $table) {
            $table->id();
            $table->string('company')->default('');
            $table->string('gender')->default('');
            $table->string('address1')->default('');
            $table->string('address2')->default('');
            $table->string('postcode')->default('');
            $table->string('city')->default('');
            $table->string('state_code')->default('');
            $table->string('state_name')->default('');
            $table->string('country_code')->default('');
            $table->string('country_name')->default('');
            $table->string('fax')->default('');
            $table->string('website')->default('');
            $table->string('region')->default('');
            $table->longText('first_name_encrypted')->nullable();
            $table->longText('last_name_encrypted')->nullable();
            $table->longText('email_encrypted')->nullable();
            $table->longText('phone_encrypted')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
