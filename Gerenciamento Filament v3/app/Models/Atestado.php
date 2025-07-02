<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class Atestado extends Model
{
    use HasFactory;
    use Notifiable;
    use HasRoles;
    use LogsActivity;


    protected $table = 'atestados';

    protected $casts = [
        'quantidade_dias' => 'string',
    ];


    protected $fillable = [
        'quantidade_dias',
        'prazo_indeterminado',
        'data_inicio',
        'data_fim',
        'cid',
        'servidor_id',
        'tipo_atestado_id',
        'substituto_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'data_inicio',
                'prazo_indeterminado',
                'quantidade_dias',
                'data_fim',
                'cid',
                'servidor_id',
                'tipo_atestado_id',
                'substituto_id',
            ]);
    }

    public function servidor()
    {
        return $this->belongsTo(Servidor::class);
    }

    public function tipoAtestado()
    {
        return $this->belongsTo(TipoAtestado::class, 'tipo_atestado_id');
    }

    public function substituto()
    {
        return $this->belongsTo(Servidor::class, 'substituto_id');
    }
}
