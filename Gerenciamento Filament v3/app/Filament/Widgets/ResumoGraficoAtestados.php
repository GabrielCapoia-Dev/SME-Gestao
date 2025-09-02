<?php

namespace App\Filament\Widgets;

use App\Models\Atestado;
use Filament\Widgets\Widget;

class ResumoGraficoAtestados extends Widget
{
    protected static ?string $heading = 'Resumo dos Atestados por Tipo';
    protected static string $view = 'filament.widgets.resumo-atestados-right';
    protected int | string | array $columnSpan = 'full';

    public array $totais = [
        'geral' => 0,
        'por_tipo' => [],
    ];

    public bool $semResultados = false;

    /** 
     * Escuta o evento disparado pela página `ListAtestados`
     * que envia os IDs filtrados da tabela
     */
    protected $listeners = ['atestadosFiltradosAtualizados' => 'atualizarResumo'];


    public function mount(): void
    {
        $this->carregarTotais();
    }

    protected function carregarTotais(?array $idsAtestados = null): void
    {
        $query = Atestado::query()->with('tipoAtestado');

        if (!empty($idsAtestados)) {
            $query->whereIn('id', $idsAtestados);
        }

        $atestados = $query->get();

        $dados = [];
        foreach ($atestados as $atestado) {
            if (!$atestado->tipoAtestado) {
                continue;
            }
            $tipo = $atestado->tipoAtestado->nome;
            $dados[$tipo] = ($dados[$tipo] ?? 0) + 1;
        }

        ksort($dados);

        $this->totais = [
            'geral'    => array_sum($dados),
            'por_tipo' => $dados,
        ];
    }


    public function atualizarResumo(array $idsAtestados = [], array $idsServidores = [], bool $hasFilters = false): void
    {
        if ($hasFilters && empty($idsAtestados)) {
            $this->totais = ['geral' => 0, 'por_tipo' => []];
            $this->semResultados = true;
            return;
        }

        $this->semResultados = false;
        $this->carregarTotais($idsAtestados);
    }


    public function getEstatisticas(): array
    {
        if ($this->semResultados || empty($this->totais['por_tipo'])) {
            return [
                'tipo_mais_comum' => null,
                'tipo_menos_comum' => null,
                'diversidade' => 0,
                'concentracao' => 0,
            ];
        }

        $tipos = $this->totais['por_tipo'];

        // Tipo com mais atestados
        $tipoMaisComum = array_keys($tipos, max($tipos))[0];

        // Tipo com menos atestados
        $tiposComAtestados = array_filter($tipos, fn($qtd) => $qtd > 0);
        $tipoMenosComum = !empty($tiposComAtestados)
            ? array_keys($tiposComAtestados, min($tiposComAtestados))[0]
            : null;

        // Diversidade = quantos tipos diferentes
        $diversidade = count($tiposComAtestados);

        // Concentração = % do tipo mais comum sobre o total
        $total = array_sum($tipos);
        $concentracao = $total > 0 ? round((max($tipos) / $total) * 100, 1) : 0;

        return [
            'tipo_mais_comum' => $tipoMaisComum,
            'tipo_menos_comum' => $tipoMenosComum,
            'diversidade' => $diversidade,
            'concentracao' => $concentracao,
        ];
    }
}
