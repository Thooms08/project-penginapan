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
        Schema::create('surcharge_settings', function (Blueprint $table) {
            $table->id();

            // Tipe biaya tambahan: early_checkin atau late_checkout
            $table->enum('type', ['early_checkin', 'late_checkout'])
                  ->comment('early_checkin = tamu datang lebih awal, late_checkout = tamu keluar terlambat');

            // Batas waktu pemicu biaya
            // early_checkin : jika tamu check-in SEBELUM jam ini → kena biaya
            // late_checkout : jika tamu check-out SETELAH jam ini → kena biaya
            $table->time('threshold_time')
                  ->comment('Jam batas pemicu biaya (H:i:s)');

            // Besaran biaya
            $table->enum('fee_type', ['fixed', 'percent'])
                  ->default('fixed')
                  ->comment('fixed = nominal Rupiah, percent = % dari harga kamar per malam');

            $table->unsignedBigInteger('fee_amount')
                  ->default(0)
                  ->comment('Nilai biaya: nominal Rp jika fixed, atau persen (0-100) jika percent');

            // Label singkat untuk tampilan admin & visitor
            $table->string('label', 120)->nullable()
                  ->comment('Contoh: "Early Check-In (sebelum 10:00)"');

            $table->text('description')->nullable()
                  ->comment('Keterangan tambahan yang tampil ke visitor');

            // Status aktif / nonaktif
            $table->boolean('is_active')->default(true)
                  ->comment('true = aktif, false = dinonaktifkan admin');

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surcharge_settings');
    }
};
