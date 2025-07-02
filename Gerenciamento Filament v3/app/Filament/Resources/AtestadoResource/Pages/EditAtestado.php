<?php

namespace App\Filament\Resources\AtestadoResource\Pages;

use App\Exceptions\SubstitutoIgualServidorException;
use App\Filament\Resources\AtestadoResource;
use App\Services\AtestadoService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditAtestado extends EditRecord
{
    protected static string $resource = AtestadoResource::class;

        protected static ?string $title = 'Editar Afastamento';

    public function mutateFormDataBeforeSave(array $data): array
    {

        $servidor_id = (string) $data['servidor_id'];
        $substituto_id = (string) $data['substituto_id'];

        if ($servidor_id === $substituto_id) {
            Notification::make()
                ->title('Atenção.')
                ->body('O servidor não pode substituir a si mesmo.')
                ->danger()
                ->persistent()
                ->send();

            $this->halt();
        }

        $dataInicio = (int) $data['data_inicio'];
        $dataFim = (int) $data['data_fim'];

        if (AtestadoService::validarDataRetroativa($dataInicio, $dataFim) === false) {
            Notification::make()
                ->title('Atenção.')
                ->body('Impossivel registrar um afastamento com data retroativa')
                ->danger()
                ->persistent()
                ->send();

            $this->halt();
        }


        $data['quantidade_dias'] = AtestadoService::calcularQuantidadeDias(
            $data['data_inicio'] ?? null,
            $data['data_fim'] ?? null,
            $data['prazo_indeterminado'] ?? false,
        );

        return $data;
    }

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
