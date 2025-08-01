<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class TurnoPolicy
{
    /**
     * Determine whether the User can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('Listar Turnos');
    }

    /**
     * Determine whether the User can view the model.
     */
    public function view(User $user, Role $role): bool
    {
        return $user->hasPermissionTo('Listar Turnos');
    }

    /**
     * Determine whether the User can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('Criar Turnos');

    }

    /**
     * Determine whether the User can update the model.
     */
    public function update(User $user, Role $role): bool
    {
        return $user->hasPermissionTo('Editar Turnos');

    }

    /**
     * Determine whether the User can delete the model.
     */
    public function delete(User $user, Role $role): bool
    {
        return $user->hasPermissionTo('Excluir Turnos');

    }

    // /**
    //  * Determine whether the User can restore the model.
    //  */
    // public function restore(User $user, Role $role): bool
    // {
    //     return false;
    // }

    // /**
    //  * Determine whether the User can permanently delete the model.
    //  */
    // public function forceDelete(User $user, Role $role): bool
    // {
    //     return false;
    // }
}

