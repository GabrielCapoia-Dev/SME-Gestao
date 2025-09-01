<?php

namespace App\Filament\Resources\AtestadoResource\Widgets;

use App\Models\Atestado;
use App\Models\TipoAtestado;
use Filament\Forms;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class AtestadosPorTiposChart extends ApexChartWidget
{
    protected static ?string $chartId = 'atestadosPorTiposChart';
    protected static ?string $heading = 'Atestados por Tipo';
    protected static ?int $contentHeight = 330;
    protected int | string | array $columnSpan = 'full';


    /** Livewire v2: $listeners; se estiver no v3 prefira #[On(...)] */
    protected $listeners = ['atestadosFiltradosAtualizados' => 'onIdsAtualizados'];

    protected array $totais = [
        'geral' => 0,
        'por_tipo' => [],
    ];

    public array $idsFiltrados = [];
    public bool $hasFilters = false;
    public ?array $lastOptions = null;
    public bool $semResultados = false;


    /**
     * Adiciona um pequeno formulário no cabeçalho do widget para alternar quais tipos aparecem.
     * Deixar vazio significa "mostrar todos os tipos".
     *
     * @return array<int, \Filament\Forms\Components\Component>
     */
    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('tipos')
                ->label('Exibir tipos')
                ->multiple()
                ->preload()
                ->searchable()
                ->options(
                    fn() => TipoAtestado::query()
                        ->orderBy('nome')
                        ->pluck('nome', 'nome')
                        ->all()
                )
                ->hint('Vazio = todos os tipos')
                ->live(), // Filament v3: atualiza o widget na mudança (no v2 use ->reactive())
        ];
    }


    public function onIdsAtualizados(array $idsAtestados = [], array $idsServidores = [], bool $hasFilters = false): void
    {
        $this->idsFiltrados = $idsAtestados ?? [];
        $this->hasFilters   = (bool) $hasFilters;
        $this->semResultados = $hasFilters && empty($idsAtestados);

        $this->updateOptions();
    }

    /**
     * Constrói as opções do ApexCharts usando tanto os Atestados filtrados quanto fallback.
     */
    protected function getOptions(): array
    {
        $selectedTipos = (array) ($this->filterFormData['tipos'] ?? []);

        // Caso tenha filtros ativos mas nenhum resultado
        if ($this->semResultados) {
            $this->totais = ['geral' => 0, 'por_tipo' => []];
            $this->dispatch('totaisAtualizadosAtestados', $this->totais);

            return [
                'chart' => [
                    'type' => 'bar',
                    'height' => 370,
                    'id' => static::$chartId,
                    'stacked' => false,
                    'toolbar' => ['show' => false]
                ],
                'xaxis' => ['categories' => []],
                'series' => [],
                'legend' => ['position' => 'top'],
                'noData' => ['text' => 'Nenhum resultado encontrado para os filtros aplicados'],
            ];
        }

        // Se vier ids filtrados → busca apenas eles
        if (! empty($this->idsFiltrados)) {
            $atestados = Atestado::query()
                ->whereIn('id', $this->idsFiltrados)
                ->with('tipoAtestado')
                ->get();
        } else {
            // fallback inicial (primeiro load, sem filtros)
            $atestados = Atestado::query()->with('tipoAtestado')->get();
        }

        $dados = $this->construirDados($atestados);

        if (! empty($selectedTipos)) {
            $dados = array_intersect_key($dados, array_flip($selectedTipos));
        }

        return $this->lastOptions = $this->montarGraficoAPartirDosDados($dados);
    }


    /**
     * Constrói matriz de dados tipo × quantidade a partir de uma coleção de Atestados.
     *
     * @param \Illuminate\Support\Collection<int, \App\Models\Atestado> $atestados
     * @return array<string, int>
     */
    protected function construirDados($atestados): array
    {
        $dados = [];

        foreach ($atestados as $atestado) {
            if (! $atestado->tipoAtestado) {
                continue;
            }

            $tipo = $atestado->tipoAtestado->nome;
            $dados[$tipo] = ($dados[$tipo] ?? 0) + 1;
        }

        // Ordena por nome do tipo para consistência visual
        ksort($dados);

        return $dados;
    }

    /**
     * Converte dados em opções do ApexCharts
     */
    protected function montarGraficoAPartirDosDados(array $dados): array
    {
        // Se não houver dados após o filtro, devolve gráfico vazio "elegante"
        if ($dados === []) {
            $this->totais = ['geral' => 0, 'por_tipo' => []];
            $this->dispatch('totaisAtualizadosAtestados', $this->totais);

            return [
                'chart' => [
                    'type' => 'bar',
                    'height' => 370,
                    'id' => static::$chartId,
                    'stacked' => false,
                    'toolbar' => ['show' => false]
                ],
                'xaxis' => ['categories' => []],
                'series' => [],
                'legend' => ['position' => 'top'],
                'noData' => ['text' => 'Sem dados para os tipos selecionados'],
            ];
        }

        $tipos = array_keys($dados);
        $valores = array_values($dados);
        $totalGeral = array_sum($valores);

        $this->totais = [
            'geral' => $totalGeral,
            'por_tipo' => $dados,
        ];
        $this->dispatch('totaisAtualizadosAtestados', $this->totais);

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 370,
                'id' => static::$chartId,
                'stacked' => false,
                'toolbar' => ['show' => false]
            ],
            'xaxis' => [
                'categories' => $tipos,
                'axisBorder' => ['show' => false],
                'axisTicks' => ['show' => false],
                'labels' => [
                    'style' => [
                        'fontSize' => '12px',
                        'fontWeight' => '500',
                    ]
                ]
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontSize' => '12px',
                    ]
                ]
            ],
            'series' => [
                [
                    'name' => 'Quantidade de Atestados',
                    'data' => $valores
                ],
            ],
            'legend' => ['position' => 'top'],
            'colors' => ['#0b9ff5', '#f59e0b', '#10b981', '#ef4444', '#8b5cf6', '#06b6d4'],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'shade' => 'dark',
                    'type' => 'vertical',
                    'shadeIntensity' => 0.5,
                    'gradientToColors' => ['#0b9ff5', '#f59e0b', '#10b981', '#ef4444', '#8b5cf6', '#06b6d4'],
                    'inverseColors' => true,
                    'opacityFrom' => 1,
                    'opacityTo' => 1,
                    'stops' => [0, 100],
                ],
            ],
            'plotOptions' => [
                'bar' => [
                    'horizontal' => false,
                    'borderRadius' => 6,
                    'borderRadiusApplication' => 'end',
                    'columnWidth' => '70%',
                ],
            ],
            'dataLabels' => [
                'enabled' => true,
                'style' => [
                    'fontSize' => '12px',
                    'fontWeight' => 'bold'
                ]
            ],
            'grid' => [
                'show' => true,
                'strokeDashArray' => 4,
                'borderColor' => '#f1f3f9',
                'xaxis' => ['lines' => ['show' => true]],
                'yaxis' => ['lines' => ['show' => true]],
                'padding' => [
                    'top' => 0,
                    'right' => 0,
                    'bottom' => 0,
                    'left' => 0
                ]
            ],
            'markers' => ['size' => 0],
            'stroke' => ['curve' => 'smooth', 'width' => 2],
            'tooltip' => [
                'enabled' => true,
                'shared' => true,
                'intersect' => false,
                'y' => [
                    'formatter' => "function(val) { return val + ' atestados' }",
                ],
                'style' => [
                    'fontSize' => '12px',
                ]
            ],
            'responsive' => [
                [
                    'breakpoint' => 480,
                    'options' => [
                        'chart' => ['height' => 250],
                        'xaxis' => [
                            'labels' => [
                                'rotate' => -45,
                                'style' => ['fontSize' => '10px']
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
