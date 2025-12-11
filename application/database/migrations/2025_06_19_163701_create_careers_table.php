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
        if (!Schema::hasTable('careers')) {
            Schema::create('careers', function (Blueprint $table) {
                $table->id();
                $table->string('title');                        // Job title
                $table->string('slug')->unique();              // For SEO-friendly URLs
                $table->string('department')->nullable();      // Example: IT, HR, Marketing
                $table->enum('job_type', ['full-time', 'part-time', 'internship', 'contract']);
                $table->enum('job_level', ['entry', 'mid', 'senior', 'manager'])->nullable();
                $table->integer('vacancy')->nullable();        // Number of openings
                $table->text('description');                   // Full job description
                $table->text('responsibilities')->nullable();  // Optional detailed list
                $table->text('requirements')->nullable();      // Qualifications & experience
                $table->text('education')->nullable();         // Degree or certification info
                $table->string('location')->nullable();        // City or remote
                $table->string('salary')->nullable();          // Salary range or "Negotiable"
                $table->string('apply_email')->nullable();     // Email for CV submission
                $table->string('apply_url')->nullable();       // Link to apply (external)
                $table->date('deadline')->nullable();          // Last date to apply
                $table->date('published_at')->nullable();      // Date published
                $table->enum('job_status', ['published', 'unpublished'])->default('published');
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->string('meta_keywords')->nullable();

                $table->tinyInteger('status')->default(0);
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
        Schema::dropIfExists('careers');
    }
};
