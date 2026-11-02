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
        Schema::table('user_documents', function (Blueprint $table) {
            $table->foreignId('visa_application_id')
                ->nullable()
                ->after('user_id')
                ->constrained('visa_applications')
                ->nullOnDelete();

            $table->index('visa_application_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_documents', function (Blueprint $table) {
            $table->dropForeign(['visa_application_id']);
            $table->dropIndex(['visa_application_id']);
            $table->dropColumn('visa_application_id');
        });
    }
};