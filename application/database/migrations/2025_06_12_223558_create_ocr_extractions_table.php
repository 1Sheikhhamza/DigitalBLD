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
        if (!Schema::hasTable('ocr_extractions')) {
            Schema::create('ocr_extractions', function (Blueprint $table) {
                $table->id();
                $table->integer('volume_id')->nullable();
                $table->string('book_volume')->nullable();
                $table->year('published_year')->nullable();
                $table->string('published_month')->nullable();
                $table->integer('starting_page_no')->nullable();
                $table->integer('ending_page_no')->nullable();
                $table->string('division')->nullable(); // e.g., Appellate Division, High Court Division
                $table->string('decided_on')->nullable(); // Date of Judgment
                $table->text('judges')->nullable();
                $table->text('parties')->nullable();
                $table->text('petitioners')->nullable();
                $table->text('respondent')->nullable();
                $table->text('related_act_order_rule')->nullable();
                $table->text('sections_subsections')->nullable();
                $table->text('key_words')->nullable();
                $table->text('subject')->nullable();
                $table->string('case_no')->nullable();
                $table->string('jurisdiction')->nullable();
                $table->longText('judgment')->nullable(); // Main judgment content
                $table->string('file_path')->nullable(); // Path to the PDF file

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('o_c_r_extractions');
    }
};
