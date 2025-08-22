<?php

namespace App\Filament\Resources\AtestadoResource\Widgets;

use App\Models\Servidor;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ServidorAtestadoChart extends ApexChartWidget
{
    protected static ?string $chartId = 'servidorAtestadoChart';
    protected static ?string $heading = 'Servidores com Atestado por Cargo e Regime';
    protected static ?int    $contentHeight = 318;

    /** Escuta os IDs vindos da ListAtestados (veja a page abaixo) */
    protected $listeners = ['atestadosFiltradosAtualizados' => 'onIdsAtualizados'];

    protected array $totais = [
        'geral' => 0,
        'por_regime' => [],
    ];

    public array $idsFiltrados = [];
    public bool $hasFilters = false;
    public ?array $lastOptions = null;

    public function onIdsAtualizados(array $ids = [], bool $hasFilters = false): void
    {
        $this->idsFiltrados = $ids ?? [];
        $this->hasFilters   = (bool) $hasFilters;
        $this->updateOptions();
    }

    protected function getOptions(): array
    {
        // 1) Se a lista emitiu IDs de servidores (após filtros), usa exatamente esses:
        if (!empty($this->idsFiltrados)) {
            $servidores = Servidor::query()
                ->whereIn('id', $this->idsFiltrados)
                ->with(['cargo.regimeContratual'])
                ->get();

            $matriz = [];
            foreach ($servidores as $s) {
                if (!$s->cargo || !$s->cargo->regimeContratual) continue;
                $cargo  = $s->cargo->nome;
                $regime = $s->cargo->regimeContratual->nome;
                $matriz[$cargo][$regime] = ($matriz[$cargo][$regime] ?? 0) + 1;
            }

            $options = $this->montarGraficoAPartirDaMatriz($matriz);
            return $this->lastOptions = $options;
        }

        // 2) Fallback: 1º load/sem filtros -> todos servidores que têm pelo menos um atestado
        $servidores = Servidor::query()
            ->whereHas('atestados')
            ->with(['cargo.regimeContratual'])
            ->get();

        $matriz = [];
        foreach ($servidores as $s) {
            if (!$s->cargo || !$s->cargo->regimeContratual) continue;
            $cargo  = $s->cargo->nome;
            $regime = $s->cargo->regimeContratual->nome;
            $matriz[$cargo][$regime] = ($matriz[$cargo][$regime] ?? 0) + 1;
        }

        $options = $this->montarGraficoAPartirDaMatriz($matriz);
        return $this->lastOptions = $options;
    }

    /** Converte matriz cargo×regime em opções do ApexCharts (igual ao gráfico de Servidores) */
    protected function montarGraficoAPartirDaMatriz(array $dados): array
    {
        $cargos = array_keys($dados);
        sort($cargos);

        $regimesSet = [];
        foreach ($dados as $cargoData) {
            foreach ($cargoData as $regime => $count) {
                $regimesSet[$regime] = true;
            }
        }
        $regimes = array_keys($regimesSet);
        sort($regimes);

        $series = [];
        $totaisPorRegime = array_fill_keys($regimes, 0);
        $totalGeral = 0;

        foreach ($regimes as $regime) {
            $data = [];
            foreach ($cargos as $cargo) {
                $valor = (int) ($dados[$cargo][$regime] ?? 0);
                $data[] = $valor;
                $totaisPorRegime[$regime] += $valor;
                $totalGeral += $valor;
            }
            $series[] = ['name' => $regime, 'data' => $data];
        }

        $this->totais = [
            'geral' => $totalGeral,
            'por_regime' => $totaisPorRegime,
        ];

        // >>> importante: mesmo nome de evento que o Resumo já escuta
        $this->dispatch('totaisAtualizados', $this->totais);

        return [
            'chart' => ['type' => 'bar', 'height' => 250, 'stacked' => false, 'toolbar' => ['show' => false]],
            'xaxis' => ['categories' => $cargos, 'axisBorder' => ['show' => false], 'axisTicks' => ['show' => false]],
            'series' => $series,
            'legend' => ['position' => 'top'],
            'colors' => ['#0b9ff5', '#f59e0b', '#10b981', '#ef4444'],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'shade' => 'dark',
                    'type' => 'vertical',
                    'shadeIntensity' => 0.5,
                    'gradientToColors' => ['#0b9ff5', '#f59e0b', '#10b981', '#ef4444'],
                    'inverseColors' => true,
                    'opacityFrom' => 1,
                    'opacityTo' => 1,
                    'stops' => [0, 100],
                ],
            ],
            'plotOptions' => [
                'bar' => ['horizontal' => false, 'borderRadius' => 5, 'borderRadiusApplication' => 'end'],
            ],
            'dataLabels' => ['enabled' => true],
            'grid' => [
                'show' => true,
                'strokeDashArray' => 4,
                'borderColor' => '#f1f3f9',
                'xaxis' => ['lines' => ['show' => true]],
                'yaxis' => ['lines' => ['show' => true]],
            ],
            'markers' => ['size' => 0],
            'stroke'  => ['curve' => 'smooth', 'width' => 3],
            'tooltip' => ['enabled' => true],
        ];
    }
}
