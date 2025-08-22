<?php

namespace App\Services;

use App\Models\User;
use Filament\Facades\Filament;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;
use Illuminate\Support\Str;

class GoogleService
{
    public function registrarOuLogar(SocialiteUserContract $oauthUser): User|null  
    {
        if ($this->validaUser($oauthUser)) {
            return $this->loginGoogle($oauthUser);
        }
        return $this->registroGoogle($oauthUser);

        return null;
    }

    private function validaUser(SocialiteUserContract $oauthUser): bool
    {
        $user = User::where('email', $oauthUser->getEmail())->first();

        if ($user && $user->email_approved) {

            \Filament\Notifications\Notification::make()
                ->title('Sucesso!')
                ->body('Bem-vindo ao Painel.')
                ->success()
                ->send();

            return true;
        }
        return false;
    }

    private function loginGoogle(SocialiteUserContract $oauthUser): User|null  
    {
        $user = User::where('email', $oauthUser->getEmail())->first();

        \Filament\Notifications\Notification::make()
            ->title('Acesso Permitido')
            ->body('Bem-vindo de volta!')
            ->success()
            ->send();

        return $user;
    }

    private function registroGoogle(SocialiteUserContract $oauthUser): User
    {
        // Cria novo usuário e vincula SocialiteUser
        $user = User::create([
            'name' => $oauthUser->getName() ?? 'Usuário Sem Nome',
            'email' => $oauthUser->getEmail(),
            'password' => bcrypt(Str::random(16)),
            'email_approved' => false,
            'email_verified_at' => null,
        ]);

        \Filament\Notifications\Notification::make()
            ->title('Cadastro Realizado')
            ->body('Usuário cadastrado com sucesso. Solicite aprovação do administrador para acessar o painel.')
            ->success()
            ->send();

        return $user;
    }
}