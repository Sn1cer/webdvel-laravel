<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Mengizinkan kolom-kolom ini diisi data (Mass Assignment)
    protected $fillable = [
        'nama_produk',
        'kategori_gender', 
        'deskripsi',
        'ukuran',
        'harga',
        'stok',
        'gambar',
        'gambar_2', 
        'gambar_3',
        'gambar_4',
        'gambar_5',
        'gambar_6',
        'gambar_7',
        'gambar_8',
    ];
    
    public function sizes()
    {
        return $this->hasMany(ProductSize::class);
    }
}