<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class BaseSalarial extends Model
{
    use HasFactory;
    use Notifiable;
    use HasRoles;
    use LogsActivity;



    protected $fillable = [
        'sigla',
        'baseSalarial',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'sigla',
                'baseSalarial',
            ]);
    }


    public function servdires()
    {
        return $this->hasMany(Servidor::class);
    }
}
