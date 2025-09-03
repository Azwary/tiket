<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supir extends Model
{
    use HasFactory;

    protected $table = 'supir';
    protected $primaryKey = 'id_supir';
    public $timestamps = true;

    protected $fillable = [
        'nama_supir',
        'no_hp',
        'status',
    ];
    public function kendaraans()
    {
        return $this->hasMany(Kendaraan::class, 'id_supir', 'id_supir');
    }
    public function jadwals()
    {
        return $this->hasMany(Jadwal::class, 'id_supir');
    }
}
