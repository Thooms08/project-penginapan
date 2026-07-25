<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Tipe diskon: none = tidak ada, percentage = persen (%), fixed = nominal (Rp)
            $table->enum('discount_type', ['none', 'percentage', 'fixed'])
                  ->default('none')
                  ->after('status');

            // Nilai diskon: persentase (cth. 15 = 15%) atau nominal (cth. 50000 = Rp50.000)
            $table->decimal('discount_value', 12, 2)
                  ->default(0)
                  ->after('discount_type');

            // Minimum malam untuk diskon berlaku (0 = berlaku untuk semua booking)
            $table->unsignedSmallInteger('discount_min_nights')
                  ->default(0)
                  ->after('discount_value');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['discount_type', 'discount_value', 'discount_min_nights']);
        });
    }
};
