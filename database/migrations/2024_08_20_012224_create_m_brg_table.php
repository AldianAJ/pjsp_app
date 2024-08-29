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
        Schema::create('m_brg', function (Blueprint $table) {
            $table->string('brg_id')->primary();
            $table->string('slug')->unique();
            $table->string('nm_brg')->unique();
            $table->string('qty');
            $table->string('qty_satuan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_brg');
    }
};
