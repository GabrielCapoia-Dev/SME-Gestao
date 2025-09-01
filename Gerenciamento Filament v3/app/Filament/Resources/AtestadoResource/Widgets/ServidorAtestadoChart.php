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
    protected static ?int    $contentHeight = 308;

    /** Livewire v2: $listeners; se estiver no v3 prefira #[On(...)] */
    protected $listeners = ['atestadosFiltradosAtualizados' => 'onIdsAtualizados'];

    protected array $totais = [
        'geral' => 0,
        'por_regime' => [],
    ];

    public array $idsFiltrados = [];
    public bool $hasFilters = false;
    public ?array $lastOptions = null;
    public bool $semResultados = false;

    /**
     * Add a small form on the widget header to toggle which cargos appear.
     * Leaving it empty means "show all cargos".
     *
     * @return array<int, \Filament\Forms\Components\Component>
     */
    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('cargos')
                ->label('Exibir cargos')
                ->multiple()
                ->preload()
                ->searchable()
                // usamos nome->nome para casar com as chaves da matriz ($cargo = $s->cargo->nome)
                ->options(
                    fn() => Cargo::query()
                        ->orderBy('nome')
                        ->pluck('nome', 'nome')
                        ->all()
                )
                ->hint('Vazio = todos os cargos')
                ->live(), // Filament v3: atualiza o widget na mudança (no v2 use ->reactive())
        ];
    }

    public function onIdsAtualizados(array $idsAtestados = [], array $idsServidores = [], bool $hasFilters = false): void
    {
        $this->idsFiltrados = $idsServidores ?? [];
        $this->hasFilters   = (bool) $hasFilters;
        $this->semResultados = $hasFilters && empty($idsServidores);

        $this->updateOptions();
    }

    /**
     * Build ApexCharts options using either the filtered Servidores or fallback.
     */
    protected function getOptions(): array
    {
        $selectedCargos = (array) ($this->filterFormData['cargos'] ?? []);

        // Caso tenha filtros ativos mas nenhum resultado
        if ($this->semResultados) {
            $this->totais = ['geral' => 0, 'por_regime' => []];
            $this->dispatch('totaisAtualizados', $this->totais);

            return [
                'chart' => [
                    'type' => 'bar',
                    'height' => 300,
                    'id' => static::$chartId,
                    'stacked' => false,
                    'toolbar' => ['show' => false],
                ],
                'xaxis' => ['categories' => []],
                'series' => [],
                'legend' => ['position' => 'top'],
                'noData' => ['text' => 'Nenhum resultado encontrado para os filtros aplicados'],
            ];
        }

        // Se vier ids filtrados → busca apenas eles
        if (! empty($this->idsFiltrados)) {
            $servidores = Servidor::query()
                ->whereIn('id', $this->idsFiltrados)
                ->with(['cargo.regimeContratual'])
                ->get();
        } else {
            // fallback inicial: todos servidores que têm pelo menos um atestado
            $servidores = Servidor::query()
                ->whereHas('atestados')
                ->with(['cargo.regimeContratual'])
                ->get();
        }

        $matriz = $this->construirMatriz($servidores);

        if (! empty($selectedCargos)) {
            $matriz = array_intersect_key($matriz, array_flip($selectedCargos));
        }

        return $this->lastOptions = $this->montarGraficoAPartirDaMatriz($matriz);
    }

    /**
     * Build cargo×regime matrix from a Servidor collection.
     *
     * @param \Illuminate\Support\Collection<int, \App\Models\Servidor> $servidores
     * @return array<string, array<string, int>>
     */
    protected function construirMatriz($servidores): array
    {
        $matriz = [];

        foreach ($servidores as $s) {
            if (! $s->cargo || ! $s->cargo->regimeContratual) {
                continue;
            }
            $cargo  = $s->cargo->nome;
            $regime = $s->cargo->regimeContratual->nome;

            $matriz[$cargo][$regime] = ($matriz[$cargo][$regime] ?? 0) + 1;
        }

        return $matriz;
    }

    /** Converts matrix into ApexCharts options (unchanged from your version) */
    protected function montarGraficoAPartirDaMatriz(array $dados): array
    {
        // Se não houver dados após o filtro, devolve gráfico vazio “elegante”
        if ($dados === []) {
            $this->totais = ['geral' => 0, 'por_regime' => []];
            $this->dispatch('totaisAtualizados', $this->totais);

            return [
                'chart' => ['type' => 'bar', 'height' => 250, 'id' => static::$chartId, 'stacked' => false, 'toolbar' => ['show' => false]],
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
            'chart' => ['type' => 'bar', 'height' => 300, 'id' => static::$chartId, 'stacked' => false, 'toolbar' => ['show' => false]],
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
