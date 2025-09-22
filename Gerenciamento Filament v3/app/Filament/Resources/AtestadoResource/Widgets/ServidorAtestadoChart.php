<?php

namespace App\Filament\Resources\AtestadoResource\Widgets;

use App\Models\Servidor;
use App\Models\Cargo;
use Filament\Forms;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ServidorAtestadoChart extends ApexChartWidget
{
    protected static ?string $chartId = 'servidorAtestadoChart';
    protected static ?string $heading = 'Servidores com Atestado por Cargo e Regime';
    protected static ?int    $contentHeight = 318;

    protected $listeners = ['atestadosFiltradosAtualizados' => 'onIdsAtualizados'];

    protected array $totais = [
        'geral' => 0,
        'por_regime' => [],
    ];

    public array $idsFiltrados = [];
    public bool $hasFilters = false;
    public ?array $lastOptions = null;
    public bool $semResultados = false;

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('cargos')
                ->label('Exibir cargos')
                ->multiple()
                ->preload()
                ->searchable()
                ->options(
                    fn() => Cargo::query()
                        ->orderBy('nome')
                        ->pluck('nome', 'nome')
                        ->all()
                )
                ->hint('Vazio = todos os cargos')
                ->live()
                ->afterStateUpdated(fn () => $this->updateOptions()),
        ];
    }

    public function onIdsAtualizados(array $idsAtestados = [], array $idsServidores = [], bool $hasFilters = false): void
    {
        $this->idsFiltrados  = $idsServidores ?? [];
        $this->hasFilters    = (bool) $hasFilters;
        $this->semResultados = $hasFilters && empty($idsServidores);
        $this->updateOptions();
    }

    protected function getOptions(): array
    {
        $selectedCargos = array_values(array_filter((array) ($this->filterFormData['cargos'] ?? [])));

        $query = Servidor::query()
            ->with(['lotacao.cargo.regimeContratual']);

        if (! empty($this->idsFiltrados)) {
            $query->whereIn('id', $this->idsFiltrados);
        } else {
            $query->whereHas('atestados');
        }

        $servidores = $query->get();

        $matriz = $this->construirMatriz($servidores);

        if ($selectedCargos !== []) {
            $matriz = array_intersect_key($matriz, array_flip($selectedCargos));
        }

        return $this->lastOptions = $this->montarGraficoAPartirDaMatriz($matriz);
    }

    protected function construirMatriz($servidores): array
    {
        $matriz = [];

        foreach ($servidores as $s) {
            $cargo  = $s->lotacao?->cargo?->nome;
            $regime = $s->lotacao?->cargo?->regimeContratual?->nome;

            if (! $cargo || ! $regime) {
                continue;
            }

            $matriz[$cargo][$regime] = ($matriz[$cargo][$regime] ?? 0) + 1;
        }

        return $matriz;
    }

    protected function montarGraficoAPartirDaMatriz(array $dados): array
    {
        if ($dados === []) {
            $this->totais = ['geral' => 0, 'por_regime' => []];
            $this->dispatch('totaisAtualizados', $this->totais);

            return [
                'chart' => ['type' => 'bar', 'height' => 300, 'stacked' => false, 'toolbar' => ['show' => false]],
                'xaxis' => ['categories' => []],
                'series' => [],
                'legend' => ['position' => 'top'],
                'noData' => ['text' => 'Sem dados para os cargos selecionados'],
            ];
        }

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
            'chart' => ['type' => 'bar', 'height' => 300, 'stacked' => false, 'toolbar' => ['show' => false]],
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
