<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class Cargo extends Model
{
    use HasFactory;
    use Notifiable;
    use HasRoles;
    use LogsActivity;

    protected $table = 'cargos';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nome',
        'descricao',
        'regime_contratual_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'nome',
                'descricao',
                'regime_contratual_id',
            ]);
    }


    public function servidores()
    {
        return $this->hasMany(Servidor::class);
    }

    public function regimeContratual()
    {
        return $this->belongsTo(RegimeContratual::class, 'regime_contratual_id');
    }

    public function lotacoes()
    {
        return $this->hasMany(Lotacao::class);
    }
}
