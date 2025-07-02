<?php

namespace App\Filament\Resources\AtestadoResource\Pages;

use App\Filament\Resources\AtestadoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAtestados extends ListRecords
{
    protected static string $resource = AtestadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Registrar Afastamento'),
        ];
    }
}
