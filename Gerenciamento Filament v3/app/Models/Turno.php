<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class Turno extends Model
{
    use HasFactory;
    use Notifiable;
    use HasRoles;
    use LogsActivity;


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nome',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'nome',
            ]);
    }

    public function servidores()
    {
        return $this->hasMany(Servidor::class);
    }
    
    public function declaracaoDeHoras()
    {
        return $this->hasMany(Servidor::class);
    }
}
