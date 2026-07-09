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
        'bukti_pembayaran', 'resi', 'tipe_pesanan', 'snap_token' 
    ];

    // Relasi ke tabel OrderDetail
    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }

    // --- AKSESOR NOMOR PESANAN CERDAS ---
    public function getNomorPesananAttribute()
    {
        $urutan = static::where('tipe_pesanan', $this->tipe_pesanan)
                        ->where('id', '<=', $this->id)
                        ->count();
                        
        if ($this->tipe_pesanan === 'Booking') {
            $prefix = 'BKG';
        } elseif ($this->tipe_pesanan === 'POS Offline') {
            $prefix = 'POS';
        } else {
            $prefix = 'ONL'; 
        }

        return $prefix . '-' . str_pad($urutan, 5, '0', STR_PAD_LEFT);
    }
}