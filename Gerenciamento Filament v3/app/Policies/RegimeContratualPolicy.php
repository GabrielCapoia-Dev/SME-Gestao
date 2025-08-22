<?php

namespace App\Policies;

use App\Models\RegimeContratual;
use App\Models\User;

class RegimeContratualPolicy
{
    /**
     * Determine whether the User can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('Listar Regimes Contratuais');
    }

    /**
     * Determine whether the User can view the model.
     */
    public function view(User $user, RegimeContratual $regimeContratual): bool
    {
        return $user->hasPermissionTo('Listar Regimes Contratuais');
    }

    /**
     * Determine whether the User can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('Criar Regimes Contratuais');

    }

    /**
     * Determine whether the User can update the model.
     */
    public function update(User $user, RegimeContratual $regimeContratual): bool
    {
        return $user->hasPermissionTo('Editar Regimes Contratuais');

    }

    /**
     * Determine whether the User can delete the model.
     */
    public function delete(User $user, RegimeContratual $regimeContratual): bool
    {
        return $user->hasPermissionTo('Excluir Regimes Contratuais');

    }

    // /**
    //  * Determine whether the User can restore the model.
    //  */
    // public function restore(User $user, RegimeContratual $regimeContratual): bool
    // {
    //     return false;
    // }

    // /**
    //  * Determine whether the User can permanently delete the model.
    //  */
    // public function forceDelete(User $user, RegimeContratual $regimeContratual): bool
    // {
    //     return false;
    // }
}

