<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class Servidor extends Model
{
    use HasFactory;
    use Notifiable;
    use HasRoles;
    use LogsActivity;


    protected $table = 'servidores';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'matricula',
        'data_admissao',
        'nome',
        'email',
        'cargo_id',
        'turno_id',
        'lotacao_id',
        'user_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'matricula',
                'data_admissao',
                'nome',
                'email',
                'cargo_id',
                'turno_id',
                'lotacao_id',
                'user_id',
            ]);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cargo()
    {
        return $this->belongsTo(Cargo::class);
    }

    public function turno()
    {
        return $this->belongsTo(Turno::class);
    }

    public function setores()
    {
        return $this->belongsToMany(Setor::class, 'servidor_setor');
    }

    public function cargaHoraria()
    {
        return $this->hasOne(CargaHoraria::class);
    }

    public function lotacao()
    {
        return $this->belongsTo(\App\Models\Lotacao::class);
    }

    public function atestados()
    {
        return $this->hasMany(Atestado::class);
    }

    public function professor()
    {
        return $this->hasOne(Professor::class);
    }
}
