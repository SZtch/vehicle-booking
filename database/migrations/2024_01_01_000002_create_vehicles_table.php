<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('plate_number')->unique();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->year('year')->nullable();
            $table->enum('type', ['angkutan_orang', 'angkutan_barang']);
            $table->enum('ownership', ['owned', 'rented'])->default('owned');
            $table->enum('status', ['available', 'in_use', 'maintenance'])->default('available');
            $table->date('last_service')->nullable();
            $table->date('next_service')->nullable();
            $table->integer('odometer')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
