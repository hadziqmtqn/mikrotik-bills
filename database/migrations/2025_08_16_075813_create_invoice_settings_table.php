<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoice_settings', function (Blueprint $table) {
            $table->id();
            $table->uuid('slug');
            $table->boolean('setup_auto_recurring_invoice')->default(true);
            $table->integer('repeat_every_date')->nullable();
            $table->integer('due_date_after');
            $table->integer('due_date_after_new_service');
            $table->integer('cancel_after');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_settings');
    }
};
