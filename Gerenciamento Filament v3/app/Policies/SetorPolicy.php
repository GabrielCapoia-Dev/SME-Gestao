<?php

namespace App\Policies;

use App\Models\Setor;
use App\Models\User;

class SetorPolicy
{
    /**
     * Determine whether the User can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('Listar Setores');
    }

    /**
     * Determine whether the User can view the model.
     */
    public function view(User $user, Setor $setor): bool
    {
        return $user->hasPermissionTo('Listar Setores');
    }

    /**
     * Determine whether the User can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('Criar Setores');

    }

    /**
     * Determine whether the User can update the model.
     */
    public function update(User $user, Setor $setor): bool
    {
        return $user->hasPermissionTo('Editar Setores');

    }

    /**
     * Determine whether the User can delete the model.
     */
    public function delete(User $user, Setor $setor): bool
    {
        return $user->hasPermissionTo('Excluir Setores');

    }

    // /**
    //  * Determine whether the User can restore the model.
    //  */
    // public function restore(User $user, Setor $setor): bool
    // {
    //     return false;
    // }

    // /**
    //  * Determine whether the User can permanently delete the model.
    //  */
    // public function forceDelete(User $user, Setor $setor): bool
    // {
    //     return false;
    // }
}

