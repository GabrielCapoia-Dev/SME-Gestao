<?php

namespace App\Services;

use App\Models\Servidor;
use App\Models\Setor;
use App\Models\RegimeContratual;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


class ServidorService
{

    public function obterServidores(): array
    {
        return Servidor::all()->toArray();
    }

    public function obterSetores(): array
    {
        return Setor::all()->toArray();
    }

    public function servidorPorSetor(): array
    {
        return Servidor::with('setores')->get()->toArray();
    }

    public function servidoresPorCargoERegime(?int $setorId = null, ?\App\Models\User $user = null): array
    {
        $user = $user ?? Auth::user();

        $regimeTable = (new RegimeContratual)->getTable();
        $hasRegime   = Schema::hasTable($regimeTable);

        // base
        $query = Servidor::query()
            ->join('cargos', 'cargos.id', '=', 'servidores.cargo_id');

        $joinedSetor   = false;
        $countExpr     = 'COUNT(servidores.id)'; // padrão sem join com setor
        $precisaDistinct = false;

        // === Escopo pelo usuário logado (mesma lógica da listagem) ===

        // 1) setores do servidor vinculado ao usuário
        if ($user?->servidor) {
            $userSetorIds = $user->servidor->setores()->pluck('setors.id')->all();
            if (!empty($userSetorIds)) {
                $query->join('servidor_setor', 'servidor_setor.servidor_id', '=', 'servidores.id');
                $joinedSetor = true;
                $query->whereIn('servidor_setor.setor_id', $userSetorIds);
                $precisaDistinct = true;
            }
        }

        // 2) setor diretamente vinculado ao usuário
        if ($user?->setor) {
            if (!$joinedSetor) {
                $query->join('servidor_setor', 'servidor_setor.servidor_id', '=', 'servidores.id');
                $joinedSetor = true;
            }
            $query->where('servidor_setor.setor_id', $user->setor->id);
            $precisaDistinct = true;
        }

        // 3) filtro adicional por setor (parâmetro do método)
        if ($setorId) {
            if (!$joinedSetor) {
                $query->join('servidor_setor', 'servidor_setor.servidor_id', '=', 'servidores.id');
                $joinedSetor = true;
            }
            $query->where('servidor_setor.setor_id', $setorId);
            $precisaDistinct = true;
        }

        if ($precisaDistinct) {
            $countExpr = 'COUNT(DISTINCT servidores.id)';
        }

        $selects = [
            'cargos.nome as cargo',
            DB::raw("$countExpr as total"),
        ];

        if ($hasRegime) {
            $selects[] = DB::raw('rc.nome as regime');
            $query->leftJoin("$regimeTable as rc", 'rc.id', '=', 'cargos.regime_contratual_id')
                ->groupBy('cargos.nome', 'rc.nome')
                ->orderBy('cargos.nome');
        } else {
            $selects[] = DB::raw("'-' as regime");
            $query->groupBy('cargos.nome')
                ->orderBy('cargos.nome');
        }

        $rows = $query->select($selects)->get();

        // Monta matriz cargo × regime
        $resultado = [];
        foreach ($rows as $row) {
            $cargo  = $row->cargo ?? '—';
            $regime = $row->regime ?? '—';
            $resultado[$cargo][$regime] = (int) $row->total;
        }

        return $resultado;
    }
}
