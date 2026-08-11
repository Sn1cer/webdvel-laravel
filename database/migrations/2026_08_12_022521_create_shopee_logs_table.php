<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('shopee_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('product_size_id')->constrained('product_sizes')->onDelete('cascade');
            $table->integer('jumlah_penyesuaian'); 
            $table->string('keterangan')->default('Penyesuaian Stok Manual (Shopee)');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('shopee_logs');
    }
};