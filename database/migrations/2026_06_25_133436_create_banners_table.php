<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('gambar'); // Untuk menyimpan nama file foto
            $table->string('judul')->nullable(); // Tulisan utama (opsional)
            $table->string('subjudul')->nullable(); // Tulisan kecil di bawah judul (opsional)
            $table->boolean('is_active')->default(true); // Untuk mematikan/menyalakan banner tanpa menghapusnya
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};