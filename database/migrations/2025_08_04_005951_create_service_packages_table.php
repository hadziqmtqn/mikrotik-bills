<?php

use App\Enums\LimitType;
use App\Enums\PackageLimitType;
use App\Enums\PaymentType;
use App\Enums\ServiceType;
use App\Enums\TimeLimitType;
use App\Enums\ValidityUnit;
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
            $table->enum('service_type', array_keys(ServiceType::options()));
            $table->string('package_name');
            $table->enum('payment_type', array_keys(PaymentType::options()));
            $table->enum('plan_type', ['pribadi', 'bisnis']);
            $table->enum('package_limit_type', array_keys(PackageLimitType::options()))->nullable();
            $table->enum('limit_type', array_keys(LimitType::options()))->nullable();
            $table->integer('time_limit')->nullable();
            $table->enum('time_limit_unit', array_keys(TimeLimitType::options()))->nullable();
            $table->integer('data_limit')->nullable();
            $table->enum('data_limit_unit', ['MBs', 'GBs'])->nullable();
            $table->integer('validity_period')->nullable();
            $table->enum('validity_unit', array_keys(ValidityUnit::options()))->nullable();
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
