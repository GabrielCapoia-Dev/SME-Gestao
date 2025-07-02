<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class Professor extends Model
{
    use HasFactory;
    use Notifiable;
    use HasRoles;
    use LogsActivity;

    protected $table = 'professors';

    protected $fillable = [
        'servidor_id',
        'turma_id',
        'aula_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'servidor_id',
                'turma_id',
                'aula_id',
            ]);
    }

    public function servidor()
    {
        return $this->belongsTo(Servidor::class, 'servidor_id', 'id');
    }

    public function aula()
    {
        return $this->belongsTo(Aula::class);
    }

    public function turma()
    {
        return $this->belongsTo(Turma::class);
    }
}
