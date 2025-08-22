<?php

namespace App\Policies;

use App\Models\TipoAtestado;
use App\Models\User;

class TipoAtestadoPolicy
{
    /**
     * Determine whether the User can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('Listar Tipos de Atestados');
    }

    /**
     * Determine whether the User can view the model.
     */
    public function view(User $user, TipoAtestado $tipoAtestado): bool
    {
        return $user->hasPermissionTo('Listar Tipos de Atestados');
    }

    /**
     * Determine whether the User can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('Criar Tipos de Atestados');

    }

    /**
     * Determine whether the User can update the model.
     */
    public function update(User $user, TipoAtestado $tipoAtestado): bool
    {
        return $user->hasPermissionTo('Editar Tipos de Atestados');

    }

    /**
     * Determine whether the User can delete the model.
     */
    public function delete(User $user, TipoAtestado $tipoAtestado): bool
    {
        return $user->hasPermissionTo('Excluir Tipos de Atestados');

    }

    // /**
    //  * Determine whether the User can restore the model.
    //  */
    // public function restore(User $user, TipoAtestado $tipoAtestado): bool
    // {
    //     return false;
    // }

    // /**
    //  * Determine whether the User can permanently delete the model.
    //  */
    // public function forceDelete(User $user, TipoAtestado $tipoAtestado): bool
    // {
    //     return false;
    // }
}

