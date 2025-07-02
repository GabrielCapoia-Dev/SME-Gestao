<?php

namespace App\Filament\Resources\TipoAtestadoResource\Pages;

use App\Filament\Resources\TipoAtestadoResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTipoAtestados extends ManageRecords
{
    protected static string $resource = TipoAtestadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
