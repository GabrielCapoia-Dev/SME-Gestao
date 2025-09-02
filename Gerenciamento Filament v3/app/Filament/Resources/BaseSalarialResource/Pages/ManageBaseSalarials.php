<?php

namespace App\Filament\Resources\BaseSalarialResource\Pages;

use App\Filament\Resources\BaseSalarialResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageBaseSalarials extends ManageRecords
{
    protected static string $resource = BaseSalarialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
