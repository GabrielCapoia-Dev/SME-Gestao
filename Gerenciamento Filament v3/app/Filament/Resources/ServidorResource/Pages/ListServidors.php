<?php

namespace App\Filament\Resources\ServidorResource\Pages;

use App\Filament\Resources\ServidorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class ListServidors extends ListRecords
{
    protected static string $resource = ServidorResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }

    protected function getHeaderWidgets(): array
    {
        return ServidorResource::getWidgets();
    }

    protected function getTableQuery(): Builder
    {
        // 👉 Agora já carrega também cargo dentro da lotação
        $query = static::getResource()::getEloquentQuery()
            ->with(['setores', 'cargaHoraria', 'lotacao.cargo.regimeContratual']);

        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // Se o usuário tiver vínculo com servidor → limita pelos setores dele
        if ($user?->servidor) {
            $userSetorIds = $user->servidor->setores()->pluck('setors.id')->toArray();

            if (!empty($userSetorIds)) {
                $query->whereHas('setores', function ($q) use ($userSetorIds) {
                    $q->whereIn('setors.id', $userSetorIds);
                });
            }
        }

        // Se o usuário tiver vínculo direto com um setor → limita só aquele setor
        if ($user?->setor) {
            $query->whereHas('setores', function ($q) use ($user) {
                $q->where('setors.id', $user->setor->id);
            });
        }

        return $query;
    }

    protected function dispatchChartPayload(): void
    {
        $ids = $this->getFilteredTableQuery()->pluck('id')->all();
        $hasFilters = $this->computeHasFiltersSafely();

        $this->dispatch('servidoresFiltradosAtualizados', $ids, $hasFilters);
    }

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
