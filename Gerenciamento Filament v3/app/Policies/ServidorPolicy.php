<?php

namespace App\Policies;

use App\Models\Servidor;
use App\Models\User;

class ServidorPolicy
{
    /**
     * Determine whether the User can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('Listar Servidores');
    }

    /**
     * Determine whether the User can view the model.
     */
    public function view(User $user, Servidor $servidor): bool
    {
        return $user->hasPermissionTo('Listar Servidores');
    }

    /**
     * Determine whether the User can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('Criar Servidores');

    }

    /**
     * Determine whether the User can update the model.
     */
    public function update(User $user, Servidor $servidor): bool
    {
        return $user->hasPermissionTo('Editar Servidores');

    }

    /**
     * Determine whether the User can delete the model.
     */
    public function delete(User $user, Servidor $servidor): bool
    {
        return $user->hasPermissionTo('Excluir Servidores');

    }

    // /**
    //  * Determine whether the User can restore the model.
    //  */
    // public function restore(User $user, Servidor $servidor): bool
    // {
    //     return false;
    // }

    // /**
    //  * Determine whether the User can permanently delete the model.
    //  */
    // public function forceDelete(User $user, Servidor $servidor): bool
    // {
    //     return false;
    // }
}

