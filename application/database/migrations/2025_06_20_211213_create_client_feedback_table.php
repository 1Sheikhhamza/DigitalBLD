<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('client_feedback')) {
            Schema::create('client_feedback', function (Blueprint $table) {
                $table->id();
                $table->string('client_name');
                $table->string('client_position')->nullable();
                $table->text('feedback');
                $table->string('client_photo')->nullable();
                $table->tinyInteger('rating')->default(5); // out of 5
                $table->string('company')->nullable();
                $table->string('website')->nullable();
                $table->tinyInteger('status')->default(1); // 1 = active, 0 = inactive
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_feedbacks');
    }
};
