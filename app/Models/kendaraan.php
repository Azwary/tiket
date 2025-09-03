<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    use HasFactory;

    protected $table = 'kendaraan';
    protected $primaryKey = 'id_kendaraan';
    public $timestamps = true;

    protected $fillable = [
        'plat_nomor',
        'id_supir',
        'status',
    ];
    public function supir()
    {
        return $this->belongsTo(Supir::class, 'id_supir', 'id_supir');
    }
    public function jadwals()
    {
        return $this->hasMany(Jadwal::class, 'id_kendaraan');
    }
}
