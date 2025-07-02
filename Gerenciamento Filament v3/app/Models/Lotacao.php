<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class Lotacao extends Model
{
    use HasFactory;
    use Notifiable;
    use HasRoles;
    use LogsActivity;

    protected $table = 'lotacoes';


    protected $fillable = [
        'nome',
        'codigo',
        'descricao',
        'setor_id',
        'cargo_id',
    ];


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'nome',
                'codigo',
                'descricao',
                'setor_id',
                'cargo_id',
            ]);
    }

    public function setor()
    {
        return $this->belongsTo(Setor::class);
    }

    public function cargo()
    {
        return $this->belongsTo(Cargo::class);
    }

    public function servidores()
    {
        return $this->hasMany(Servidor::class);
    }

}
