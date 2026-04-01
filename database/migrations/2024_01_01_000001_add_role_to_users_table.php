<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'approver'])->default('admin')->after('email');
            $table->unsignedTinyInteger('approval_level')->nullable()->after('role');
            $table->string('phone')->nullable()->after('approval_level');
            $table->string('department')->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'approval_level', 'phone', 'department']);
        });
    }
};
