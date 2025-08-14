<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->uuid('slug');
            $table->string('short_name');
            $table->string('full_name')->nullable();
            $table->enum('navigation_position', ['top', 'left'])->default('left');
            $table->string('panel_color')->default('amber');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
