<?php

namespace App\Providers\Filament;

use Illuminate\Support\Str;
use App\Livewire\PasswordReset;
use App\Models\User;
use App\Services\DominioEmailService;
use App\Services\GoogleService;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Rmsramos\Activitylog\ActivitylogPlugin;
use Filament\Http\Middleware\Authenticate;
use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use DutchCodingCompany\FilamentSocialite\Models\SocialiteUser;
use DutchCodingCompany\FilamentSocialite\Provider;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->routes(function () {
                Route::get('/password-reset', PasswordReset::class);
            })
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Sky,
                // 'gray' => Color::Gray,
                'gray' => [
                    50 => '#ebf8faff',
                    100 => '#d2fafcc7',
                    200 => '#c0d4d4ff',
                    300 => '#c7caccff',
                    400 => '#a0a0a0ff',
                    500 => '#929292ff',
                    600 => '#5c5c66ff',
                    700 => '#374151',
                    800 => '#1f2937',
                    900 => '#0e1930ff',
                    950 => '#081124ff',
                ],
            ])
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                // Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                FilamentApexChartsPlugin::make(),


                ActivitylogPlugin::make()
                    ->label('Registro de Atividade')
                    ->pluralLabel('Registro de Atividades')
                    ->navigationGroup('Administrativo')
                    ->isRestoreActionHidden(true)
                    ->navigationItem(false)
                    ->isResourceActionHidden(true)
                    ->isRestoreModelActionHidden(true)
                    ->translateSubject(
                        fn($label) => __("models." . \Illuminate\Support\Str::snake($label), [], 'pt_BR') !== "models." . Str::snake($label)
                            ? __("models." . \Illuminate\Support\Str::snake($label), [], 'pt_BR')
                            : $label
                    )
                    ->navigationSort(1)
                    ->authorize(function () {
                        /** @var \App\Models\User|null $user */
                        $user = Auth::user();

                        // Se não estiver autenticado, esconde
                        if (!$user) {
                            return false;
                        }

                        // Mostra só para Admin
                        return $user->hasRole('Admin');
                    }),

                // FilamentSocialitePlugin::make()
                //     ->providers([
                //         'google' => Provider::make('google')->label('Google'),
                //     ])
                //     ->registration(true)
                //     ->createUserUsing(function (string $provider, SocialiteUserContract $oauthUser) {
                //         $service = new GoogleService();

                //         $email = $oauthUser->getEmail();

                //         if (!app('App\Services\DominioEmailService')->isEmailAutorizado($email)) {
                //             throw new \App\Exceptions\EmailNaoAutorizado('Email não é permitido para cadastro, entre em contato com o administrador.');
                //         }

                //         // Verifica se já existe um SocialiteUser com esse provider e provider_id
                //         $existingSocialite = SocialiteUser::where('provider', $provider)
                //             ->where('provider_id', $oauthUser->getId())
                //             ->first();

                //         if ($existingSocialite) {
                //             return $existingSocialite->user;
                //         }
                //         $user = $service->registrarOuLogar($oauthUser);

                //         return $user;
                //     })
            ]);
    }
}
