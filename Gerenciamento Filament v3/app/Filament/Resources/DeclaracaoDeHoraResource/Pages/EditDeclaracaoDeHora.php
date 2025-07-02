<?php

namespace App\Filament\Resources\DeclaracaoDeHoraResource\Pages;

use App\Filament\Resources\DeclaracaoDeHoraResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Services\DeclaracaoHoraService;
use Filament\Notifications\Notification;

class EditDeclaracaoDeHora extends EditRecord
{
    protected static string $resource = DeclaracaoDeHoraResource::class;

    protected static ?string $title = 'Registrar Declaração de Hora';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
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

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }
}
