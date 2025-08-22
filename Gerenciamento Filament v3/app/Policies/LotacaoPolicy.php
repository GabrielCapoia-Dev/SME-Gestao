<?php

namespace App\Policies;

use App\Models\Lotacao;
use App\Models\User;

class LotacaoPolicy
{
    /**
     * Determine whether the User can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('Listar Lotações');
    }

    /**
     * Determine whether the User can view the model.
     */
    public function view(User $user, Lotacao $lotacao): bool
    {
        return $user->hasPermissionTo('Listar Lotações');
    }

    /**
     * Determine whether the User can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('Criar Lotações');

    }

    /**
     * Determine whether the User can update the model.
     */
    public function update(User $user, Lotacao $lotacao): bool
    {
        return $user->hasPermissionTo('Editar Lotações');

    }

    /**
     * Determine whether the User can delete the model.
     */
    public function delete(User $user, Lotacao $lotacao): bool
    {
        return $user->hasPermissionTo('Excluir Lotações');

    }

    // /**
    //  * Determine whether the User can restore the model.
    //  */
    // public function restore(User $user, Lotacao $lotacao): bool
    // {
    //     return false;
    // }

    // /**
    //  * Determine whether the User can permanently delete the model.
    //  */
    // public function forceDelete(User $user, Lotacao $lotacao): bool
    // {
    //     return false;
    // }
}

