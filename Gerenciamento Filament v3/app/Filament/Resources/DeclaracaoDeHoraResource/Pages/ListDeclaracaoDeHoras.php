<?php

namespace App\Filament\Resources\DeclaracaoDeHoraResource\Pages;

use App\Filament\Resources\DeclaracaoDeHoraResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListDeclaracaoDeHoras extends ListRecords
{
    protected static string $resource = DeclaracaoDeHoraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Registrar Declaração de Hora'),
        ];
    }

    protected function getTableQuery(): Builder
    {
        // Evita N+1
        $query = static::getResource()::getEloquentQuery()
            ->with(['servidor.setores', 'turno']);

        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // Se o usuário tiver um Servidor vinculado, restringe pelos mesmos setores
        if ($user?->servidor) {
            $userSetorIds = $user->servidor->setores()->pluck('setors.id')->toArray();

            if (!empty($userSetorIds)) {
                $query->whereHas('servidor.setores', function ($q) use ($userSetorIds) {
                    $q->whereIn('setors.id', $userSetorIds);
                });
            }
        }

        // Se o usuário tiver um setor próprio, restringe também por ele
        if ($user?->setor) {
            $query->whereHas('servidor.setores', function ($q) use ($user) {
                $q->where('setors.id', $user->setor->id);
            });
        }

        return $query;
    }
}
