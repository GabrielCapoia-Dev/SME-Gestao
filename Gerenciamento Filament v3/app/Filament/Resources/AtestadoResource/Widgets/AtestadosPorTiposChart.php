<?php

namespace App\Filament\Resources\AtestadoResource\Widgets;

use App\Models\Atestado;
use App\Models\TipoAtestado;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class AtestadosPorTiposChart extends ApexChartWidget
{
    protected static ?string $chartId = 'atestadosPorTiposChart';
    protected static ?string $heading = 'Atestados por Tipo';
    protected static ?int $contentHeight = 318;

    protected array $totais = [
        'geral' => 0,
        'por_tipo' => [],
    ];

    public ?array $lastOptions = null;

    protected function getOptions(): array
    {
        // Conta atestados por tipo
        $dados = Atestado::query()
            ->selectRaw('tipo_atestado_id, COUNT(*) as total')
            ->groupBy('tipo_atestado_id')
            ->pluck('total', 'tipo_atestado_id')
            ->toArray();

        // Ordena por nome do tipo
        $tipos = TipoAtestado::orderBy('nome')->pluck('nome', 'id')->toArray();

        $categorias = [];
        $valores = [];
        $totaisPorTipo = [];
        $totalGeral = 0;

        foreach ($tipos as $id => $nome) {
            $qtd = (int) ($dados[$id] ?? 0);
            $categorias[] = $nome;
            $valores[] = $qtd;
            $totaisPorTipo[$nome] = $qtd;
            $totalGeral += $qtd;
        }

        $this->totais = [
            'geral' => $totalGeral,
            'por_tipo' => $totaisPorTipo,
        ];
        $this->dispatch('totaisAtualizados', $this->totais);

        // Se não houver dados, devolve gráfico vazio “elegante”
        if ($totalGeral === 0) {
            return [
                'chart' => ['type' => 'bar', 'height' => 300, 'stacked' => false, 'toolbar' => ['show' => false]],
                'xaxis' => ['categories' => []],
                'series' => [],
                'legend' => ['position' => 'top'],
                'noData' => ['text' => 'Nenhum atestado encontrado'],
            ];
        }

        return $this->lastOptions = [
            'chart' => ['type' => 'bar', 'height' => 300, 'stacked' => false, 'toolbar' => ['show' => false]],
            'xaxis' => [
                'categories' => $categorias,
                'axisBorder' => ['show' => false],
                'axisTicks' => ['show' => false],
            ],
            'series' => [
                ['name' => 'Atestados', 'data' => $valores],
            ],
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
