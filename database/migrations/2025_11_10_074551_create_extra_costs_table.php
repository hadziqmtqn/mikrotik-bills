<?php

use App\Enums\BillingType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('extra_costs', function (Blueprint $table) {
            $table->id();
            $table->uuid('slug');
            $table->string('name');
            $table->decimal('fee', 20, 0);
            $table->enum('billing_type', array_keys(BillingType::options()))->default(BillingType::RECURRING->value);
            $table->text('note')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('extra_costs');
    }
};
