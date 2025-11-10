<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inv_customer_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('customer_service_id');
            $table->decimal('amount', 20, 0);
            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            $table->foreign('customer_service_id')->references('id')->on('customer_services')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inv_customer_services');
    }
};
