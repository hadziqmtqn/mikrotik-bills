<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inv_extra_costs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('extra_cost_id');
            $table->decimal('fee', 20, 0)->default(0);
            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('invoices')->cascadeOnDelete();
            $table->foreign('extra_cost_id')->references('id')->on('extra_costs')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inv_extra_costs');
    }
};
