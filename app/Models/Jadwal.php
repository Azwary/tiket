<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Jadwal extends Model
{
    use Notifiable;

    protected $table = 'jadwal';
    protected $primaryKey = 'id_jadwal';

    protected $fillable = [
        'id_rute',
        'id_supir',
        'id_kendaraan',
        'jam_keberangkatan',
    ];

    public function pemesanan()
    {
        return $this->hasMany(Pemesanan::class, 'id_jadwal', 'id_jadwal');
    }

    public function rute()
    {
        return $this->belongsTo(Rute::class, 'id_rute', 'id_rute');
    }
    public function supir()
    {
        return $this->belongsTo(Supir::class, 'id_supir', 'id_supir');
    }

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'id_kendaraan', 'id_kendaraan');
    }
}
