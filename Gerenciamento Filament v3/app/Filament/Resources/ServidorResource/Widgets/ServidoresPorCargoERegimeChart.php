<?php

namespace App\Filament\Widgets;

use App\Models\Cargo;
use App\Models\Servidor;
use App\Services\ServidorService;
use Filament\Forms;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ServidoresPorCargoERegimeChart extends ApexChartWidget
{
    protected static ?string $chartId = 'servidoresPorCargoERegimeChart';
    protected static ?string $heading = 'Servidores por Cargo e Regime Contratual';
    protected static ?int $contentHeight = 318;

    /** Continua ouvindo o evento da page (se estiver usando Livewire v3, considere #[On]) */
    protected $listeners = ['servidoresFiltradosAtualizados' => 'onIdsAtualizados'];

    /** Totais publicados para outros widgets */
    protected array $totais = [
        'geral' => 0,
        'por_regime' => [],
    ];

    /** IDs vindos da listagem (após filtros) */
    public array $idsFiltrados = [];
    public bool $hasFilters = false;
    public ?array $lastOptions = null;

    /**
     * Small form on the widget header: choose which cargos to display.
     * Empty selection => show ALL cargos.
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
                ->options(fn () => Cargo::query()
                    ->orderBy('nome')
                    ->pluck('nome', 'nome')
                    ->all()
                )
                ->hint('Vazio = todos os cargos')
                ->live() // Filament v3: atualiza o widget ao mudar o valor
                // opcional: se quiser já reagir sem esperar re-render:
                ->afterStateUpdated(fn () => $this->updateOptions()),
        ];
    }

    /**
     * Recebe IDs da listagem (mantenho a sua assinatura).
     */
    public function onIdsAtualizados(array $ids = [], bool $hasFilters = false): void
    {
        $this->idsFiltrados = $ids ?? [];
        $this->hasFilters   = (bool) $hasFilters;
        $this->updateOptions();
    }

    /**
     * Monta as opções do ApexCharts:
     * - Se vieram IDs: usa exatamente esses servidores.
     * - Caso contrário: fallback pelo serviço (ex.: visão geral).
     * Em ambos, aplica o filtro de cargos selecionados no Select.
     */
    protected function getOptions(): array
    {
        // seleção atual de cargos (nomes); vazio => não filtra
        $selectedCargos = array_values(array_filter((array) ($this->filterFormData['cargos'] ?? [])));

        if (! empty($this->idsFiltrados)) {
            $servidores = Servidor::query()
                ->whereIn('id', $this->idsFiltrados)
                ->with(['cargo.regimeContratual'])
                ->get();

            $matriz = $this->construirMatriz($servidores);

            if ($selectedCargos !== []) {
                $matriz = array_intersect_key($matriz, array_flip($selectedCargos));
            }

            return $this->lastOptions = $this->montarGraficoAPartirDaMatriz($matriz);
        }

        // Fallback (primeiro load / visão geral)
        /** @var ServidorService $service */
        $service = app(ServidorService::class);
        $dados   = $service->servidoresPorCargoERegime(setorId: null); // matriz [cargo][regime] => qtd

        if ($selectedCargos !== []) {
            $dados = array_intersect_key($dados, array_flip($selectedCargos));
        }

        return $this->lastOptions = $this->montarGraficoAPartirDaMatriz($dados);
    }

    /**
     * Constrói a matriz cargo×regime a partir da coleção de Servidores.
     *
     * @param \Illuminate\Support\Collection<int, \App\Models\Servidor> $servidores
     * @return array<string, array<string, int>>
     */
    protected function construirMatriz($servidores): array
    {
        $matriz = [];

        foreach ($servidores as $s) {
            if (! $s->cargo || ! $s->cargo->regimeContratual) {
                // se preferir, agrupe em "Sem Cargo/Sem Regime"
                continue;
            }

            $cargo  = $s->cargo->nome;
            $regime = $s->cargo->regimeContratual->nome;

            $matriz[$cargo][$regime] = ($matriz[$cargo][$regime] ?? 0) + 1;
        }

        return $matriz;
    }

    /**
     * Converte matriz cargo×regime em opções do ApexCharts (com “no data” elegante).
     */
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
