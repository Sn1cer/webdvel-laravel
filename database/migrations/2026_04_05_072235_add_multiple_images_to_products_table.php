<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('gambar_2')->nullable()->after('gambar');
            $table->string('gambar_3')->nullable()->after('gambar_2');
            $table->string('gambar_4')->nullable()->after('gambar_3');
            $table->string('gambar_5')->nullable()->after('gambar_4');
            $table->string('gambar_6')->nullable()->after('gambar_5');
            $table->string('gambar_7')->nullable()->after('gambar_6');
            $table->string('gambar_8')->nullable()->after('gambar_7');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'gambar_2', 'gambar_3', 'gambar_4', 'gambar_5', 
                'gambar_6', 'gambar_7', 'gambar_8'
            ]);
        });
    }
};