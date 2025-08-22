<?php

namespace App\Filament\Resources\ProfessorResource\Pages;

use App\Filament\Resources\ProfessorResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ManageProfessors extends ManageRecords
{
    protected static string $resource = ProfessorResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }

    protected function getTableQuery(): Builder
    {
        $query = static::getResource()::getEloquentQuery()
            ->with(['servidor.setores', 'turma.setor', 'aula']);

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
            // Professor visível se (servidor dele está em um setor visível) OU (a turma dele pertence a um setor visível).
            $query->where(function (Builder $q) use ($visibleSetorIds) {
                $q->whereHas('servidor.setores', function (Builder $q2) use ($visibleSetorIds) {
                    $q2->whereIn('setors.id', $visibleSetorIds->all());
                })->orWhereHas('turma.setor', function (Builder $q3) use ($visibleSetorIds) {
                    $q3->whereIn('setors.id', $visibleSetorIds->all());
                });
            });
        } else {
            // Se quiser bloquear visualização sem vínculo, descomente:
            // $query->whereRaw('1=0');
        }

        return $query;
    }
}
