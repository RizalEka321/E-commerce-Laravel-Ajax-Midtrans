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
        Schema::create('keranjang', function (Blueprint $table) {
            $table->id('id_keranjang');
            $table->string('produk_id');
            $table->foreignId('users_id');
            $table->integer('ukuran_id');
            $table->integer('jumlah');
            $table->string('ukuran');
            $table->enum('status', ['Ya', 'Tidak']);
            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('produk_id')->references('id_produk')->on('produk')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keranjang');
    }
};
