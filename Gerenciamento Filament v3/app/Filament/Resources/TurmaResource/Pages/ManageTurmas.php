<?php

namespace App\Filament\Resources\TurmaResource\Pages;

use App\Filament\Resources\TurmaResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class ManageTurmas extends ManageRecords
{
    protected static string $resource = TurmaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        $query = static::getResource()::getEloquentQuery()->with(['setor', 'nomeTurma', 'siglaTurma']);

        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if ($user->servidor) {
            $userSetorIds = $user->servidor->setores()
                ->select('setors.id')
                ->pluck('setors.id')
                ->toArray();

            if (!empty($userSetorIds)) {
                $query->whereIn('setor_id', $userSetorIds);
            }
        }

        return $query;
    }
}
