<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('availability_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultant_id')->constrained('users')->cascadeOnDelete();
            $table->date('date');
            $table->string('time');
            $table->boolean('is_booked')->default(false);
            $table->timestamps();

            $table->unique(['consultant_id', 'date', 'time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('availability_slots');
    }
};