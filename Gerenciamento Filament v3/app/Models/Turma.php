<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class Turma extends Model
{
    use HasFactory;
    use Notifiable;
    use HasRoles;
    use LogsActivity;

    protected $table = 'turmas';

    protected $fillable = [
        'nome_turma_id',
        'descricao',
        'setor_id',
        'sigla_turma_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'nome_turma_id',
                'descricao',
                'setor_id',
                'sigla_turma_id',
            ]);
    }

    public function nomeCompleto(): string
    {
        $nome = $this->nomeTurma?->nome ?? '';
        $sigla = $this->siglaTurma?->nome ?? '';
        return trim("{$nome} {$sigla}");
    }


    public function setor()
    {
        return $this->belongsTo(Setor::class);
    }

    public function nomeTurma()
    {
        return $this->belongsTo(NomeTurma::class);
    }

    public function siglaTurma()
    {
        return $this->belongsTo(SiglaTurma::class);
    }

    public function professores()
    {
        return $this->hasMany(Professor::class);
    }
}
