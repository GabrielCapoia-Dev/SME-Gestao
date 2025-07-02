<?php

namespace App\Filament\Resources\DeclaracaoDeHoraResource\Pages;

use App\Filament\Resources\DeclaracaoDeHoraResource;
use Filament\Resources\Pages\CreateRecord;
use App\Services\DeclaracaoHoraService;
use Filament\Notifications\Notification;

class CreateDeclaracaoDeHora extends CreateRecord
{
    protected static string $resource = DeclaracaoDeHoraResource::class;

    protected static ?string $title = 'Registrar Declaração de Hora';

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }



    protected function mutateFormDataBeforeCreate(array $data): array
    {

        // dd($data);

        if (DeclaracaoHoraService::horarioRetroativo($data['hora_inicio'], $data['hora_fim'])) {
            Notification::make()
                ->title('Atenção.')
                ->body('O horário de fim não pode ser anterior ao início.')
                ->danger()
                ->persistent()
                ->send();

            $this->halt();
        }

        $data['carga_horaria'] = DeclaracaoHoraService::calcularCargaHoraria(
            $data['hora_inicio'],
            $data['hora_fim']
        );

        return $data;
    }
}
