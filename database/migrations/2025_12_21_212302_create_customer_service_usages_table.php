<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customer_service_usages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_service_id');
            $table->unsignedBigInteger('invoice_id');
            $table->dateTime('period_start');
            $table->dateTime('period_end');
            $table->dateTime('next_billing_date');
            $table->integer('days_of_usage');
            $table->decimal('daily_price', 20, 0);
            $table->decimal('total_price', 20, 0);
            $table->boolean('mark_done')->default(false);
            $table->boolean('inv_generated')->default(false);
            $table->timestamps();

            $table->foreign('customer_service_id')->references('id')->on('customer_services')->cascadeOnDelete();
            $table->foreign('invoice_id')->references('id')->on('invoices')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_service_usages');
    }
};
