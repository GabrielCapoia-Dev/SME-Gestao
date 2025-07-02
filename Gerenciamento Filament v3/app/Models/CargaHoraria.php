<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class CargaHoraria extends Model
{

    use HasFactory;
    use Notifiable;
    use HasRoles;
    use LogsActivity;


    protected $fillable = [
        'servidor_id',
        'entrada',
        'saida_intervalo',
        'entrada_intervalo',
        'saida',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'servidor_id',
                'entrada',
                'saida_intervalo',
                'entrada_intervalo',
                'saida',
            ]);
    }



    public function servidor()
    {
        return $this->belongsTo(Servidor::class);
    }
}
