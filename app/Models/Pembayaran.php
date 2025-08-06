<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Pembayaran extends Model
{
    use Notifiable;
    protected $table = 'pembayaran';
    protected $primaryKey = 'id_pembayaran';
    protected $fillable = [
        'id_pemesanan',
        'jumlah_pembayaran',
        'upload_bukti',
        'batas_waktu_pembayaran',
        'status_konfirmasi',
    ];
    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class, 'id_pemesanan', 'id_pemesanan');
    }
}
