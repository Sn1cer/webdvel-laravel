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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nama_depan');
            $table->string('nama_belakang');
            $table->string('alamat_jalan');
            $table->string('wilayah'); // Menyimpan Provinsi, Kota, dll
            $table->string('no_hp');
            $table->text('alamat_lengkap')->nullable();
            $table->integer('total_harga');
            $table->string('status')->default('Belum Bayar'); // Status: Belum Bayar, Diproses, Dikirim
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
