<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('slug');
            $table->integer('serial_number')->unique();
            $table->string('code')->unique();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('invoice_id');
            $table->enum('payment_method', ['cash', 'bank_transfer']);
            $table->unsignedBigInteger('bank_account_id')->nullable();
            $table->decimal('amount', 20, 0)->default(0);
            $table->date('date');
            $table->enum('status', ['pending', 'paid', 'partially_paid', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('invoice_id')->references('id')->on('invoices')->restrictOnDelete();
            $table->foreign('bank_account_id')->references('id')->on('bank_accounts')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
