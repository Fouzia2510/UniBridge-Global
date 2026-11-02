<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('universities', function (Blueprint $table) {

            $table->id();

            $table->string('name');
            $table->string('country');
            $table->string('city')->nullable();

            $table->string('degree_level');
            $table->string('subject');

            $table->decimal('tuition_fee', 10, 2)->nullable();
            $table->decimal('estimated_living_cost', 10, 2)->nullable();

            $table->decimal('min_cgpa', 4, 2)->nullable();
            $table->decimal('min_english_score', 4, 2)->nullable();
            $table->string('english_test_type')->nullable();

            $table->string('intake')->nullable();
            $table->integer('ranking')->nullable();

            $table->boolean('scholarship_available')->default(false);
            $table->boolean('post_study_work')->default(false);

            $table->text('description')->nullable();

            $table->string('website_url')->nullable();
            $table->string('application_url')->nullable();

            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('universities');
    }
};