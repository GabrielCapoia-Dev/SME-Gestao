<?php

namespace App\Filament\Resources\ServidorResource\Pages;

use App\Filament\Resources\ServidorResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateServidor extends CreateRecord
{
    protected static string $resource = ServidorResource::class;


    
    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }
}
