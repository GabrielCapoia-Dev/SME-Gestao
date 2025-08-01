<?php

namespace App\Policies;

use App\Models\Atestado;
use App\Models\User;

class AtestadoPolicy
{
    /**
     * Determine whether the User can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('Listar Afastamentos');
    }

    /**
     * Determine whether the User can view the model.
     */
    public function view(User $user, Atestado $atestado): bool
    {
        return $user->hasPermissionTo('Listar Afastamentos');
    }

    /**
     * Determine whether the User can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('Criar Afastamentos');

    }

    /**
     * Determine whether the User can update the model.
     */
    public function update(User $user, Atestado $atestado): bool
    {
        return $user->hasPermissionTo('Editar Afastamentos');

    }

    /**
     * Determine whether the User can delete the model.
     */
    public function delete(User $user, Atestado $atestado): bool
    {
        return $user->hasPermissionTo('Excluir Afastamentos');

    }

    // /**
    //  * Determine whether the User can restore the model.
    //  */
    // public function restore(User $user, Atestado $atestado): bool
    // {
    //     return false;
    // }

    // /**
    //  * Determine whether the User can permanently delete the model.
    //  */
    // public function forceDelete(User $user, Atestado $atestado): bool
    // {
    //     return false;
    // }
}

