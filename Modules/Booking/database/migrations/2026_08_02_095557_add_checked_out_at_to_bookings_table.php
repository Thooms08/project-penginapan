<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->timestamp('checked_in_at')->nullable()
                  ->after('booking_status')
                  ->comment('Timestamp when admin confirmed check-in');
            $table->timestamp('checked_out_at')->nullable()
                  ->after('checked_in_at')
                  ->comment('Timestamp when admin confirmed check-out');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['checked_in_at', 'checked_out_at']);
        });
    }
};
