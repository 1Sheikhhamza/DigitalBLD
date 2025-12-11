<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bookmarks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('decision_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('decision_id')->references('id')->on('ocr_extractions')->onDelete('cascade');
            $table->unique(['user_id', 'decision_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookmarks');
    }
};
