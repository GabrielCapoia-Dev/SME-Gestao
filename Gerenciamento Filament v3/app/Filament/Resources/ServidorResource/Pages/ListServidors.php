<?php

namespace App\Filament\Resources\ServidorResource\Pages;

use App\Filament\Resources\ServidorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use ServidorStatsOverview;

class ListServidors extends ListRecords
{
    protected static string $resource = ServidorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
