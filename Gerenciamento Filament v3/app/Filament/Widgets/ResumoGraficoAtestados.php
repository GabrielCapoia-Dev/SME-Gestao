<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class ResumoGraficoAtestados extends Widget
{
    protected static ?string $heading = 'Resumo dos Atestados por Tipo';
    protected static string $view = 'filament.widgets.resumo-atestados-right';
    protected int | string | array $columnSpan = 'full';


    // começa vazio; será preenchido pelo evento do gráfico
    public array $totais = [
        'geral' => 0,
        'por_tipo' => [],
    ];

    // escuta o evento vindo do widget do gráfico de atestados
    protected $listeners = ['totaisAtualizadosAtestados' => 'atualizaTotais'];

    public function atualizaTotais(array $totais): void
    {
        $this->totais = $totais;
        $this->dispatch('$refresh'); // força re-render
    }

    /**
     * Retorna estatísticas adicionais para enriquecer o resumo
     */
    public function getEstatisticas(): array
    {
        $tipos = $this->totais['por_tipo'] ?? [];

        if (empty($tipos)) {
            return [
                'tipo_mais_comum' => null,
                'tipo_menos_comum' => null,
                'diversidade' => 0,
                'concentracao' => 0,
            ];
        }

        // Tipo com mais atestados
        $tipoMaisComum = array_keys($tipos, max($tipos))[0];

        // Tipo com menos atestados (excluindo zeros)
        $tiposComAtestados = array_filter($tipos, fn($qtd) => $qtd > 0);
        $tipoMenosComum = !empty($tiposComAtestados) ? array_keys($tiposComAtestados, min($tiposComAtestados))[0] : null;

        // Índice de diversidade (quantidade de tipos diferentes com atestados)
        $diversidade = count($tiposComAtestados);

        // Concentração (% do tipo mais comum em relação ao total)
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
