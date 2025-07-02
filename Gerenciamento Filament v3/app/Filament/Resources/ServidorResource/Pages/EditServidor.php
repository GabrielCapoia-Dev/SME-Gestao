<?php

namespace App\Filament\Resources\ServidorResource\Pages;

use App\Filament\Resources\ServidorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditServidor extends EditRecord
{
    protected static string $resource = ServidorResource::class;


    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }
}
