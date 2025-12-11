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
        if (!Schema::hasTable('ocr_extractions')) {
            Schema::create('ocr_extractions', function (Blueprint $table) {
                $table->id();
                $table->integer('volume_id');
                $table->string('book_volume');
                $table->year('published_year');
                $table->string('published_month');
                $table->integer('starting_page_no');
                $table->integer('ending_page_no');
                $table->string('division'); // e.g., Appellate Division, High Court Division
                $table->string('decided_on'); // Date of Judgment
                $table->text('judges');
                $table->text('parties');
                $table->text('petitioners');
                $table->text('respondent');
                $table->text('related_act_order_rule');
                $table->text('sections_subsections');
                $table->text('key_words');
                $table->text('subject');
                $table->string('case_no');
                $table->string('jurisdiction');
                $table->longText('judgment'); // Main judgment content

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
