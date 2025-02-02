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
        Schema::create('detail_pesanan', function (Blueprint $table) {
            $table->id('id_detail');
            $table->string('produk_id');
            $table->string('pesanan_id');
            $table->integer('jumlah');
            $table->string('ukuran');
            $table->string('produk');
            $table->string('foto');
            $table->bigInteger('harga');
            $table->foreign('pesanan_id')->references('id_pesanan')->on('pesanan')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail__pesanan');
    }
};
