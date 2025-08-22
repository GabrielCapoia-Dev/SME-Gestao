<?php

// App\Filament\Widgets\ServidoresPorCargoERegimeChart.php

namespace App\Filament\Widgets;

use App\Models\Servidor;
use App\Services\ServidorService;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ServidoresPorCargoERegimeChart extends ApexChartWidget
{
    protected static ?string $chartId = 'servidoresPorCargoERegimeChart';
    protected static ?string $heading = 'Servidores por Cargo e Regime Contratual';
    protected static ?int $contentHeight = 318;

    /** ===== NOVO: escuta o evento vindo da page ===== */
    protected $listeners = ['servidoresFiltradosAtualizados' => 'onIdsAtualizados'];

    protected array $totais = [
        'geral' => 0,
        'por_regime' => [],
    ];

    public array $idsFiltrados = [];
    public bool $hasFilters = false;     // pode manter, mas agora é opcional na lógica
    public ?array $lastOptions = null;

    public function onIdsAtualizados(array $ids = [], bool $hasFilters = false): void
    {
        $this->idsFiltrados = $ids ?? [];
        $this->hasFilters   = (bool) $hasFilters; // informativo
        $this->updateOptions();
    }



    protected function getOptions(): array
    {
        // 1) Se recebemos IDs (com filtros ou sem filtros), SEMPRE montar a partir deles:
        if (!empty($this->idsFiltrados)) {
            $servidores = \App\Models\Servidor::query()
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
            return $this->lastOptions = $options; // cache
        }

        // 2) Se NÃO recebemos IDs ainda (primeiro load, por ex.), caia no serviço:
        /** @var \App\Services\ServidorService $service */
        $service = app(\App\Services\ServidorService::class);
        $dados = $service->servidoresPorCargoERegime(setorId: null);

        $options = $this->montarGraficoAPartirDaMatriz($dados);
        return $this->lastOptions = $options;
    }


    /** ===== Helper: converte matriz cargo×regime em opções do ApexCharts ===== */
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
