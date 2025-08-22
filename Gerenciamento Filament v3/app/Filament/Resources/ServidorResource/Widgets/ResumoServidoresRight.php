<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class ResumoServidoresRight extends Widget
{
    protected static ?string $heading = 'Resumo dos Dados';
    protected static string $view = 'filament.widgets.resumo-servidores-right';

    // começa vazio; será preenchido pelo evento
    public array $totais = [
        'geral' => 0,
        'por_regime' => [],
    ];

    // escuta o evento vindo do widget do gráfico
    protected $listeners = ['totaisAtualizados' => 'atualizaTotais'];

    public function atualizaTotais(array $totais): void
    {
        $this->totais = $totais;
        $this->dispatch('$refresh'); // re-render
    }
}
