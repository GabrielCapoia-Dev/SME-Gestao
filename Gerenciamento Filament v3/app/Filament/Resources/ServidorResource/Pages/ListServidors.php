<?php

namespace App\Filament\Resources\ServidorResource\Pages;

use App\Filament\Resources\ServidorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use ServidorStatsOverview;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;


class ListServidors extends ListRecords
{
    protected static string $resource = ServidorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        $query = static::getResource()::getEloquentQuery()->with(['setores', 'cargaHoraria', 'lotacao']);


        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // Verifica se o usuário tem um servidor vinculado
        if ($user->servidor) {
            $userSetorIds = $user->servidor->setores()->pluck('setors.id')->toArray();

            // Se o servidor tem locais de trabalho, filtra
            if (!empty($userSetorIds)) {
                $query->whereHas('setores', function ($q) use ($userSetorIds) {
                    $q->whereIn('setors.id', $userSetorIds);
                });
            }
        }

        // Verifica se o usuário tem um setor vinculado
        if ($user->setor) {
            $query->whereHas('setores', function ($q) use ($user) {
                $q->where('setors.id', $user->setor->id);
            });
        }

        // Fallback: se não houver servidor ou setor, lista tudo
        return $query;
    }
}
