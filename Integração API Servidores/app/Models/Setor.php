<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setor extends Model
{
    protected $table = 'setors';

    protected $fillable = [
        'nome',
        'email',
        'telefone',
    ];
}
