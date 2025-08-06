<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Kursi extends Model
{
    use Notifiable;
    protected $table = 'kursi';
    protected $primaryKey = 'id_kursi';

    protected $fillable = [
        'no_kursi',
    ];
}
