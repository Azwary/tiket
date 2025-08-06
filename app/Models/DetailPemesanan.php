<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPemesanan extends Model
{
    protected $table = 'detail_pemesanan';

    protected $fillable = ['id_pemesanan', 'id_kursi', 'id_jadwal', 'id_penumpang'];

    // Relasi ke penumpang
    public function penumpang()
    {
        return $this->belongsTo(Penumpang::class, 'id_penumpang', 'id');
    }

    // Relasi ke jadwal
    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'id_jadwal');
    }

    // Relasi ke kursi (jika ada model Kursi)
    public function kursi()
    {
        return $this->belongsTo(Kursi::class, 'id_kursi', 'id_kursi');
    }

    // Relasi ke pemesanan (jika ingin akses data dari tabel `pemesanan`)
    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class, 'id_pemesanan');
    }
}
