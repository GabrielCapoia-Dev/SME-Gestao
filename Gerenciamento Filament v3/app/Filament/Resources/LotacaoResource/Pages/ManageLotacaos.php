<?php

namespace App\Filament\Resources\LotacaoResource\Pages;

use App\Filament\Resources\LotacaoResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageLotacaos extends ManageRecords
{
    protected static string $resource = LotacaoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
