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
        Schema::create('decision_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('decision_id')->constrained('ocr_extractions')->onDelete('cascade');
            $table->foreignId('sender_id')->constrained('subscribers')->onDelete('cascade');
            $table->foreignId('receiver_id')->constrained('subscribers')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('decision_shares');
    }
};
