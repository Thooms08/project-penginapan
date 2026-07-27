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
        Schema::create('check_in_out_settings', function (Blueprint $table) {
            $table->id();
            $table->date('date')->comment('Date for this setting');
            $table->enum('type', ['check_in', 'check_out'])->comment('Type: check_in or check_out');
            $table->time('time')->comment('The scheduled time');
            $table->text('notes')->nullable()->comment('Optional admin notes');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Same date can have multiple times, but same date+type+time must be unique
            $table->unique(['date', 'type', 'time'], 'unique_date_type_time');
            $table->index(['date', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('check_in_out_settings');
    }
};
