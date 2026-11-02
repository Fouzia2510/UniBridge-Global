<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultant_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('phone')->nullable();
            $table->string('qualification')->nullable();
            $table->string('specialization')->nullable();
            $table->decimal('experience_years', 4, 1)->nullable();
            $table->text('countries')->nullable();
            $table->string('languages')->nullable();
            $table->text('about')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultant_profiles');
    }
};