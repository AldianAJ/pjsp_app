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
        Schema::create('detail_krm_brg', function (Blueprint $table) {
            $table->id();
            $table->string('krm_brg_id');
            $table->string('brg_id');
            $table->string('persetujuan');
            $table->integer('jml_krm')->nullable();
            $table->string('user_id')->nullable();
            $table->string('catatan')->nullable();
            $table->string('status_krm')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_krm_brg');
    }
};
