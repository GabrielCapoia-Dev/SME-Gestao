<?php

namespace App\Providers;

use App\Models\Atestado;
use App\Models\Aula;
use App\Models\Cargo;
use App\Models\DeclaracaoDeHora;
use App\Models\DominioEmail;
use App\Models\Lotacao;
use App\Models\Permission;
use App\Models\Professor;
use App\Models\RegimeContratual;
use App\Models\Role;
use App\Models\Servidor;
use App\Models\Setor;
use App\Models\TipoAtestado;
use App\Models\Turma;
use App\Models\Turno;
use App\Models\User;
use App\Policies\AtestadoPolicy;
use App\Policies\AulaPolicy;
use App\Policies\CargoPolicy;
use App\Policies\DeclaracaoDeHoraPolicy;
use App\Policies\DominioEmailPolicy;
use App\Policies\LotacaoPolicy;
use App\Policies\PermissionPolicy;
use App\Policies\ProfessorPolicy;
use App\Policies\RegimeContratualPolicy;
use App\Policies\RolePolicy;
use App\Policies\ServidorPolicy;
use App\Policies\SetorPolicy;
use App\Policies\TipoAtestadoPolicy;
use App\Policies\TurmaPolicy;
use App\Policies\TurnoPolicy;
use App\Policies\UserPolicy;
use BladeUI\Icons\Factory;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // // Injetando html direto na tela de login 
        // FilamentView::registerRenderHook(
        //     PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
        //     fn(): string => <<< 'HTML'
        //     <div class='flex justify-end gap-1 text-sm'>
        //         <a href="/admin/password-reset" class="text-primary-500">Esqueceu sua senha?</a>
        //     </div>
        //     HTML
        // );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Turno::class, TurnoPolicy::class);
        Gate::policy(Turma::class, TurmaPolicy::class);
        Gate::policy(TipoAtestado::class, TipoAtestadoPolicy::class);
        Gate::policy(Setor::class, SetorPolicy::class);
        Gate::policy(RegimeContratual::class, RegimeContratualPolicy::class);
        Gate::policy(Professor::class, ProfessorPolicy::class);
        Gate::policy(Servidor::class, ServidorPolicy::class);
        Gate::policy(Lotacao::class, LotacaoPolicy::class);
        Gate::policy(Cargo::class, CargoPolicy::class);
        Gate::policy(Aula::class, AulaPolicy::class);
        Gate::policy(DeclaracaoDeHora::class, DeclaracaoDeHoraPolicy::class);
        Gate::policy(Permission::class, PermissionPolicy::class);
        Gate::policy(DominioEmail::class, DominioEmailPolicy::class);
        Gate::policy(Atestado::class, AtestadoPolicy::class);

        FilamentView::registerRenderHook('panels::body.end', function () {
            if (!request()->routeIs('filament.admin.resources.servidores.*')) {
                return '';
            }

            return <<<'HTML'
            <style>
                .filament-tables-filters-modal .fi-modal {
                    max-width: 80rem !important;
                    width: 100%;
                }

                .filament-tables-filters-modal .fi-modal-content {
                    padding: 2rem;
                }
            </style>
        HTML;
        });
    }
}
