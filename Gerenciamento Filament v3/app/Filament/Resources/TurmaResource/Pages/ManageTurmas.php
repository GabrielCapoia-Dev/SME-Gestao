<?php

namespace App\Filament\Resources\TurmaResource\Pages;

use App\Filament\Resources\TurmaResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ManageTurmas extends ManageRecords
{
    protected static string $resource = TurmaResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }

    protected function getTableQuery(): Builder
    {
        $query = static::getResource()::getEloquentQuery()
            ->with(['setor', 'nomeTurma', 'siglaTurma']);

        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        $visibleSetorIds = collect();

        if ($user?->servidor) {
            $visibleSetorIds = $visibleSetorIds->merge(
                $user->servidor->setores()->pluck('setors.id')
            );
        }

        if ($user?->setor) {
            $visibleSetorIds->push($user->setor->id);
        }

        $visibleSetorIds = $visibleSetorIds->unique()->filter();

        if ($visibleSetorIds->isNotEmpty()) {
            // Turma tem coluna setor_id, então o filtro é direto:
            $query->whereIn('setor_id', $visibleSetorIds->all());
        } else {
            // Se quiser bloquear visualização sem vínculo, descomente:
            // $query->whereRaw('1=0');
        }

        return $query;
    }
}
