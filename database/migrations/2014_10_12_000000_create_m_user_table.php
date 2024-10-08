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
        Schema::create('m_user', function (Blueprint $table) {
            $table->string('user_id')->primary();
            $table->string('slug')->unique();;
            $table->string('kode')->unique();
            $table->string('name')->unique();
            $table->text('address')->nullable();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('role');
            $table->string('status');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_user');
    }
};
