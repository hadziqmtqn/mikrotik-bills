<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('additional_service_fees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_service_id');
            $table->unsignedBigInteger('extra_cost_id');
            $table->decimal('fee', 20, 0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('customer_service_id')->references('id')->on('customer_services')->cascadeOnDelete();
            $table->foreign('extra_cost_id')->references('id')->on('extra_costs')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('additional_service_fees');
    }
};
