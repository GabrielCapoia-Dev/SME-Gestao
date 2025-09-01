<?php

// App\Filament\Resources\AtestadoResource\Pages\ListAtestados.php
namespace App\Filament\Resources\AtestadoResource\Pages;

use App\Filament\Resources\AtestadoResource;
use App\Filament\Resources\AtestadoResource\Widgets\ServidorAtestadoChart;
use App\Filament\Resources\AtestadoResource\Widgets\AtestadosPorTiposChart;
use App\Filament\Widgets\ResumoGraficoAtestados;
use App\Filament\Widgets\ResumoGraficoServidores;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListAtestados extends ListRecords
{
    protected static string $resource = AtestadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Registrar Afastamento'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        // Nova instância do Resumo: vai ouvir 'totaisAtualizados' emitido pelo gráfico acima
        return [
            ServidorAtestadoChart::class,
            ResumoGraficoServidores::class,
            AtestadosPorTiposChart::class,
            ResumoGraficoAtestados::class
        ];
    }

    protected function getTableQuery(): Builder
    {
        $query = static::getResource()::getEloquentQuery()
            ->with(['servidor.setores', 'tipoAtestado']);

        $user = Auth::user();

        if ($user?->servidor) {
            $userSetorIds = $user->servidor->setores()->pluck('setors.id')->toArray();
            if (!empty($userSetorIds)) {
                $query->whereHas('servidor.setores', fn($q) => $q->whereIn('setors.id', $userSetorIds));
            }
        }

        if ($user?->setor) {
            $query->whereHas('servidor.setores', fn($q) => $q->where('setors.id', $user->setor->id));
        }

        return $query;
    }

    /** Emite os IDs dos servidores presentes na listagem atual (após filtros/pesquisa) */
    protected function dispatchChartPayload(): void
    {
        $query = $this->getFilteredTableQuery();

        $idsServidores = $query->pluck('servidor_id')
            ->unique()
            ->values()
            ->all();

        $idsAtestados = $query->pluck('id')
            ->values()
            ->all();



        $hasFilters = $this->computeHasFiltersSafely();

        // Se não houver nenhum resultado, despacha arrays vazios
        if (empty($idsAtestados) && empty($idsServidores)) {
            $this->dispatch('atestadosFiltradosAtualizados', [], [], $hasFilters);

            return;
        }

        // Despacha normalmente
        $this->dispatch('atestadosFiltradosAtualizados', $idsAtestados, $idsServidores, $hasFilters);
    }


    /** Idêntico ao que você já usa para detectar filtros/busca ativos */
    protected function computeHasFiltersSafely(): bool
    {
        $hasSearch = !empty($this->tableSearch ?? '');

        try {
            $filtersState = $this->getTableFiltersForm()?->getState() ?? [];
        } catch (\Throwable $e) {
            $filtersState = [];
        }

        $hasFilterValues = false;
        foreach ($filtersState as $value) {
            if (is_array($value)) {
                if (array_filter($value) !== []) {
                    $hasFilterValues = true;
                    break;
                }
            } elseif (!empty($value)) {
                $hasFilterValues = true;
                break;
            }
        }

        return $hasSearch || $hasFilterValues;
    }

    public function updatedTableFilters(): void
    {
        parent::updatedTableFilters();
        $this->dispatchChartPayload();
    }
    public function updatedTableSearch(): void
    {
        parent::updatedTableSearch();
        $this->dispatchChartPayload();
    }
    public function updatedTableSortColumn(): void
    {
        parent::updatedTableSortColumn();
        $this->dispatchChartPayload();
    }
    public function updatedTableSortDirection(): void
    {
        parent::updatedTableSortDirection();
        $this->dispatchChartPayload();
    }
    public function updatedTableRecordsPerPage(): void
    {
        parent::updatedTableRecordsPerPage();
        $this->dispatchChartPayload();
    }
}
