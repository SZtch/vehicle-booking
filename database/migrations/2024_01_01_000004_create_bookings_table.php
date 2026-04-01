<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique();
            $table->foreignId('admin_id')->constrained('users');
            $table->foreignId('vehicle_id')->constrained();
            $table->foreignId('driver_id')->constrained();
            $table->string('purpose');
            $table->string('origin');
            $table->string('destination');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->integer('passenger_count')->default(1);
            $table->text('notes')->nullable();
            $table->enum('status', [
                'pending',
                'approved_l1',
                'approved',
                'rejected'
            ])->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
