<?php

namespace App\Filament\Resources\DeclaracaoDeHoraResource\Pages;

use App\Filament\Resources\DeclaracaoDeHoraResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Servidor;

class ListDeclaracaoDeHoras extends ListRecords
{
    protected static string $resource = DeclaracaoDeHoraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Registrar Afastamento'),
        ];
    }

    protected function getTableQuery(): Builder
    {
        $query = static::getResource()::getEloquentQuery()
            ->with(['servidor.setores', 'servidor.cargo', 'servidor.lotacao', 'turno']);

        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if ($user->servidor) {
            $userSetorIds = $user->servidor->setores()->pluck('setors.id')->toArray();

            if (!empty($userSetorIds)) {
                $query->whereHas('servidor.setores', function ($q) use ($userSetorIds) {
                    $q->whereIn('setors.id', $userSetorIds);
                });
            }
        }

        return $query;
    }
}
