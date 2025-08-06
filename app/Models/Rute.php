<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Rute extends Model
{
    use Notifiable;
    protected $table = 'rute';
    protected $primaryKey = 'id_rute';

    protected $fillable = [
        'asal',
        'tujuan',
        'harga',
    ];
    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }
}
