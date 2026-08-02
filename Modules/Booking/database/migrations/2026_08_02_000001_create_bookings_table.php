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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique()->comment('Unique booking code, e.g. BK-20260802-XXXX');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();

            // Stay dates
            $table->date('check_in_date')->comment('Planned check-in date');
            $table->date('check_out_date')->comment('Planned check-out date');
            $table->unsignedSmallInteger('nights')->comment('Total nights calculated from dates');

            // Pricing
            $table->decimal('price_per_night', 12, 2)->comment('Room price per night at time of booking');
            $table->decimal('subtotal', 12, 2)->comment('price_per_night * nights');
            $table->decimal('tax_amount', 12, 2)->default(0)->comment('Tax applied (e.g. 10%)');
            $table->decimal('total_amount', 12, 2)->comment('subtotal + tax_amount');

            // Payment
            $table->enum('payment_type', ['dp', 'full'])
                  ->comment('dp = 50% down payment, full = pay in full now');
            $table->decimal('amount_paid', 12, 2)->default(0)->comment('Amount that must be paid now (DP or full)');
            $table->decimal('amount_remaining', 12, 2)->default(0)->comment('Remaining balance to be paid at check-in');

            // Midtrans
            $table->string('midtrans_order_id')->nullable()->unique()
                  ->comment('Order ID sent to Midtrans, usually same as booking_code');
            $table->string('midtrans_transaction_id')->nullable()
                  ->comment('Transaction ID returned by Midtrans');
            $table->string('midtrans_payment_type')->nullable()
                  ->comment('e.g. bank_transfer, gopay, qris');
            $table->string('midtrans_va_number')->nullable()
                  ->comment('Virtual account number if applicable');
            $table->json('midtrans_raw')->nullable()
                  ->comment('Raw notification payload from Midtrans');

            // Status
            $table->enum('payment_status', [
                'pending',
                'paid',
                'failed',
                'expired',
                'cancelled',
            ])->default('pending')->comment('Payment status from Midtrans callback');

            $table->enum('booking_status', [
                'waiting_payment',
                'confirmed',
                'checked_in',
                'checked_out',
                'cancelled',
                'failed',
            ])->default('waiting_payment')->comment('Overall booking lifecycle status');

            // Optional guest note
            $table->text('guest_note')->nullable()->comment('Optional note from visitor');

            $table->timestamps();

            // Indexes for common queries
            $table->index(['user_id', 'booking_status']);
            $table->index(['room_id', 'check_in_date', 'check_out_date']);
            $table->index('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
