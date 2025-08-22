<?php

namespace App\Policies;

use App\Models\DeclaracaoDeHora;
use App\Models\User;

class DeclaracaoDeHoraPolicy
{
    /**
     * Determine whether the User can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('Listar Declarações de Hora');
    }

    /**
     * Determine whether the User can view the model.
     */
    public function view(User $user, DeclaracaoDeHora $declaracaoDeHora): bool
    {
        return $user->hasPermissionTo('Listar Declarações de Hora');
    }

    /**
     * Determine whether the User can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('Criar Declarações de Hora');

    }

    /**
     * Determine whether the User can update the model.
     */
    public function update(User $user, DeclaracaoDeHora $declaracaoDeHora): bool
    {
        return $user->hasPermissionTo('Editar Declarações de Hora');

    }

    /**
     * Determine whether the User can delete the model.
     */
    public function delete(User $user, DeclaracaoDeHora $declaracaoDeHora): bool
    {
        return $user->hasPermissionTo('Excluir Declarações de Hora');

    }

    // /**
    //  * Determine whether the User can restore the model.
    //  */
    // public function restore(User $user, DeclaracaoDeHora $declaracaoDeHora): bool
    // {
    //     return false;
    // }

    // /**
    //  * Determine whether the User can permanently delete the model.
    //  */
    // public function forceDelete(User $user, DeclaracaoDeHora $declaracaoDeHora): bool
    // {
    //     return false;
    // }
}

