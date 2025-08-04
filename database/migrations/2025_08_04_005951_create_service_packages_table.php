<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('service_packages', function (Blueprint $table) {
            $table->id();
            $table->uuid('slug');
            $table->integer('serial_number')->unique();
            $table->string('code')->unique();
            $table->enum('service_type', ['hotspot', 'pppoe']);
            $table->string('package_name');
            $table->enum('payment_type', ['prepaid', 'postpaid']);
            $table->enum('plan_type', ['pribadi', 'bisnis']);
            $table->enum('package_limit_type', ['unlimited', 'limited'])->nullable();
            $table->enum('limit_type', ['batas_waktu', 'data', 'keduanya'])->nullable();
            $table->integer('time_limit')->nullable();
            $table->enum('time_limit_unit', ['menit', 'jam', 'hari'])->nullable();
            $table->integer('data_limit')->nullable();
            $table->enum('data_limit_unit', ['MBs', 'GBs'])->nullable();
            $table->integer('validity_period')->nullable();
            $table->enum('validity_unit', ['meni', 'jam', 'hari', 'bulan'])->nullable();
            $table->decimal('package_price', 20, 0);
            $table->decimal('price_before_discount', 20, 0)->nullable();
            $table->unsignedBigInteger('router_id');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('router_id')->references('id')->on('routers')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_packages');
    }
};
