<?php

namespace App\Filament\Resources\AulaResource\Pages;

use App\Filament\Resources\AulaResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAulas extends ManageRecords
{
    protected static string $resource = AulaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
