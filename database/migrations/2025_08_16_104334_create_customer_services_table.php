<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customer_services', function (Blueprint $table) {
            $table->id();
            $table->uuid('slug');
            $table->string('reference_number')->unique();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('service_package_id');
            $table->decimal('price', 20, 0);
            $table->enum('package_type', ['subscription', 'one-time']);
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date_time')->nullable();
            $table->enum('status', ['pending', 'active', 'suspended', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('service_package_id')->references('id')->on('service_packages')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_services');
    }
};
