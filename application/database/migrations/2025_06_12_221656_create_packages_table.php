<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->enum('duration_type', ['monthly', 'quarterly', 'half_yearly', 'yearly'])->default('monthly');
            $table->integer('duration_in_days');
            $table->string('currency', 10)->default('৳');
            $table->string('highlight_badge')->nullable();
            $table->string('button_text')->default('Sign up Now');
            $table->integer('order')->default(0);
            $table->string('icon')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->json('features')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
