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
        Schema::create('detail_minta_brg', function (Blueprint $table) {
            $table->id();
            $table->string('permintaan_barang_id');
            $table->string('barang_id');
            $table->integer('jumlah_permintaan');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_minta_brg');
    }
};
