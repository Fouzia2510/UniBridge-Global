<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultants', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('company');
            $table->string('slug')->unique();

            $table->string('logo')->nullable();
            $table->text('short_description')->nullable();

            $table->string('city')->nullable();
            $table->string('address')->nullable();

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->string('website')->nullable();

            $table->string('reference_url')->nullable();
            $table->string('reference_label')->nullable();

            $table->json('services')->nullable();
            $table->json('destinations')->nullable();

            $table->boolean('is_verified')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultants');
    }
};