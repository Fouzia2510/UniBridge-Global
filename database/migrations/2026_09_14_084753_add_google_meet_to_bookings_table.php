<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('meeting_provider')
                ->nullable()
                ->after('status');

            $table->string('meeting_space_name')
                ->nullable()
                ->after('meeting_provider');

            $table->text('meeting_uri')
                ->nullable()
                ->after('meeting_space_name');

            $table->timestamp('meeting_created_at')
                ->nullable()
                ->after('meeting_uri');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'meeting_provider',
                'meeting_space_name',
                'meeting_uri',
                'meeting_created_at',
            ]);
        });
    }
};