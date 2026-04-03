<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    // Mengizinkan data ini diisi secara massal dari form
    protected $fillable = [
        'user_id',
        'product_id',
        'ukuran',
        'jumlah',
    ];

    // Relasi: 1 Keranjang pasti milik 1 Produk celana
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relasi: 1 Keranjang pasti milik 1 User (Pelanggan)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}