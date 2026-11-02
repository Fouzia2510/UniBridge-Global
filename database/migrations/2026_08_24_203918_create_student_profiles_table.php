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
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Academic history
            $table->string('degree')->nullable();
            $table->string('field_of_study')->nullable();
            $table->decimal('cgpa', 4, 2)->nullable();
            $table->year('graduation_year')->nullable();

            // English test score
            $table->enum('english_test', ['IELTS', 'PTE', 'Other', 'None'])->nullable();
            $table->decimal('english_score', 4, 2)->nullable();

            // Preferences
            $table->string('preferred_country')->nullable();
            $table->string('preferred_degree_level')->nullable();
            $table->string('preferred_subject')->nullable();
            $table->decimal('budget', 10, 2)->nullable();
            $table->string('preferred_intake')->nullable();
            $table->integer('work_experience_years')->nullable();

            // Readiness score (calculated later, AI-based)
            $table->integer('readiness_score')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};