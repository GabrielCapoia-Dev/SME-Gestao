<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function afterSave(): void
    {
        $usuarioLogado = Auth::user();
        $usuarioSendoEditadoId = $this->record->id;

        if ($usuarioLogado->id === $usuarioSendoEditadoId) {
            $roleAtual = $usuarioLogado->roles->first()?->id;
            $roleDoFormulario = (int) ($this->data['role'][0] ?? null);

            if ($roleDoFormulario !== $roleAtual) {
                $this->record->roles()->sync([$roleAtual]);

                Notification::make()
                    ->title('Atenção.')
                    ->body('Você não pode alterar o nível de acesso do seu próprio perfil.')
                    ->danger()
                    ->persistent()
                    ->send();

                $this->halt();
            }
        }
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $usuarioLogado = Auth::user()->id;
        $usuarioSendoEditadoId = $this->record->id;

        if (($usuarioLogado === $usuarioSendoEditadoId) && $data['email_approved'] !== true) {
            Notification::make()
                ->title('Atenção.')
                ->body('Você não pode remover autorização de acesso do seu próprio perfil.')
                ->danger()
                ->persistent()
                ->send();

            $this->halt();
        }

        if (!empty($data['email_approved']) && $data['email_approved'] && empty($data['email_verified_at'])) {
            $data['email_verified_at'] = now();
        }
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
