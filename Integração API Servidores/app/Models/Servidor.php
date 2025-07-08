<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servidor extends Model
{

    protected $table = 'servidores';

    protected $fillable = [
        'entidade',
        'matricula',
        'nome',
        'descricaoCargo',
        'descricaoLotacao',
        'descricaoClasse',
        'descricaoNatureza',
        'faixa',
        'situacao',
        'dataAdmissao',
        'dataDemissao',
        'localTrabalho',
        'horarioEntrada',
        'horarioSaidaIntervalo',
        'horarioEntradaIntervalo',
        'horarioSaida',
        'horarioEspecial',
        'horarioTrabalho',
        'horarioSaidaFormated',
        'horarioEntradaFormated',
        'horarioSaidaIntervaloFormated',
        'horarioEntradaIntervaloFormated',
    ];
}
