<?php

namespace App\Filament\Resources\RegimeContratualResource\Pages;

use App\Filament\Resources\RegimeContratualResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageRegimeContratuals extends ManageRecords
{
    protected static string $resource = RegimeContratualResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
