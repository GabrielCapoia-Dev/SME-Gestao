<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Servidor;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (!empty($data['email_approved']) && $data['email_approved']) {
            $data['email_verified_at'] = now();
        }

        return $data;
    }


    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }


    protected function handleRecordCreation(array $data): Model
    {
        $servidorId = $data['servidor_id'] ?? null;
        unset($data['servidor_id']);

        return DB::transaction(function () use ($data, $servidorId) {
            $user = User::create($data);

            if ($servidorId) {
                $servidor = Servidor::whereNull('user_id')->findOrFail($servidorId);
                $servidor->user_id = $user->id;
                $servidor->save();
            }

            return $user;
        });
    }
}
