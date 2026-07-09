<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'nama_depan', 'nama_belakang', 'alamat_jalan', 
        'wilayah', 'no_hp', 'alamat_lengkap', 'total_harga', 'ongkir', 'status', 
        'bukti_pembayaran', 'resi', 'tipe_pesanan', 'snap_token' // 
    ];

    // Relasi ke tabel OrderDetail
    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }
}