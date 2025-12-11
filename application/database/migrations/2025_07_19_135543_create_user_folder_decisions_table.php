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
        Schema::create('user_folder_decisions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');       // subscriber id
            $table->unsignedBigInteger('folder_id');     // related folder
            $table->unsignedBigInteger('decision_id');   // OCRExtraction id
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'folder_id', 'decision_id']);

            $table->foreign('user_id')->references('id')->on('subscribers')->onDelete('cascade');
            $table->foreign('folder_id')->references('id')->on('folders')->onDelete('cascade');
            $table->foreign('decision_id')->references('id')->on('ocr_extractions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_folder_decisions');
    }
};
