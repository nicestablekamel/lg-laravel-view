<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();

            $table->string('company_name');
            $table->string('company_phone')->nullable();
            $table->string('company_email')->nullable();
            $table->string('company_address')->nullable();

            $table->string('client_name');
            $table->string('client_phone');
            $table->date('reception_date');
            $table->date('recuperation_date');

            $table->string('product_category');
            $table->string('serial_number');
            $table->text('problem_description');

            $table->unsignedInteger('repair_quote')->default(0);
            $table->string('repair_status')->default('Active');
            $table->text('policy_note')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
