<?php

namespace App\Filament\Resources\DeclaracaoDeHoraResource\Pages;

use App\Filament\Resources\DeclaracaoDeHoraResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

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
}
