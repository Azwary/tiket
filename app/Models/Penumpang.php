<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Penumpang extends Authenticatable
{
    use Notifiable;
    protected $table = 'penumpang';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nama_penumpang',
        'no_telepon',
        'username',
        'password',
    ];

    protected $hidden = ['password'];
    public function detailPemesanans()
    {
        return $this->hasMany(DetailPemesanan::class, 'id_penumpang', 'id');
    }
}
