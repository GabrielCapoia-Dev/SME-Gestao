<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class Setor extends Model
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
        'email',
        'telefone',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'nome',
                'email',
                'telefone',
            ]);
    }

    public function servidores()
    {
        return $this->belongsToMany(Servidor::class, 'servidor_setor');
    }

    public function usuarios()
    {
        return $this->hasMany(User::class);
    }

    public function lotacoes()
    {
        return $this->hasMany(Lotacao::class);
    }

    public function turmas()
    {
        return $this->hasMany(Turma::class);
    }
}
