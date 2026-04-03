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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            // Menghubungkan keranjang dengan ID Pelanggan
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // Menghubungkan keranjang dengan ID Produk Celana
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            // Menyimpan pilihan ukuran dan jumlah
            $table->string('ukuran');
            $table->integer('jumlah');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
