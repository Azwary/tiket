<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Pemesanan extends Model
{
    use Notifiable;
    protected $table = 'pemesanan';
    protected $primaryKey = 'id_pemesanan';
    protected $fillable = [
        'id_penumpang',
        'id_jadwal',
        'tanggal_pemesanan',
        'tanggal_keberangkatan',
        'total_harga',
        'batas_waktu_pembayaran',
    ];
    public function penumpang()
    {
        return $this->belongsTo(Penumpang::class, 'id_penumpang', 'id');
    }
    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'id_jadwal');
    }
    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'id_pemesanan', 'id_pemesanan');
    }
    public function detail_pemesanan()
    {
        return $this->hasMany(DetailPemesanan::class, 'id_pemesanan');
    }
    public function rute()
    {
        return $this->belongsTo(Rute::class, 'id_rute');
    }
}
