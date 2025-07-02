<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class DeclaracaoDeHora extends Model
{
    use HasFactory;
    use Notifiable;
    use HasRoles;
    use LogsActivity;

    protected $fillable = [
        'servidor_id',
        'turno_id',
        'data',
        'hora_inicio',
        'hora_fim',
        'cid',
        'carga_horaria'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'servidor_id',
                'turno_id',
                'hora_inicio',
                'hora_fim',
                'cid',
                'carga_horaria'
            ]);
    }

    public function servidor()
    {
        return $this->belongsTo(Servidor::class);
    }

    public function turno()
    {
        return $this->belongsTo(Turno::class);
    }
}
