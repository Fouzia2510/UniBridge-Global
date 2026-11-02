<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->string('lead_status')
                ->default('new')
                ->after('remarks');

            $table->text('consultant_remarks')
                ->nullable()
                ->after('lead_status');

            $table->timestamp('contacted_at')
                ->nullable()
                ->after('consultant_remarks');

            $table->timestamp('converted_at')
                ->nullable()
                ->after('contacted_at');

            $table->index(['consultant_id', 'lead_status']);
        });
    }

    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->dropIndex(['consultant_id', 'lead_status']);

            $table->dropColumn([
                'lead_status',
                'consultant_remarks',
                'contacted_at',
                'converted_at',
            ]);
        });
    }
};