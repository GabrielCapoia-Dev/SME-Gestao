<?php

namespace App\Policies;

use App\Models\Turma;
use App\Models\User;

class TurmaPolicy
{
    /**
     * Determine whether the User can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('Listar Turmas');
    }

    /**
     * Determine whether the User can view the model.
     */
    public function view(User $user, Turma $turma): bool
    {
        return $user->hasPermissionTo('Listar Turmas');
    }

    /**
     * Determine whether the User can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('Criar Turmas');

    }

    /**
     * Determine whether the User can update the model.
     */
    public function update(User $user, Turma $turma): bool
    {
        return $user->hasPermissionTo('Editar Turmas');

    }

    /**
     * Determine whether the User can delete the model.
     */
    public function delete(User $user, Turma $turma): bool
    {
        return $user->hasPermissionTo('Excluir Turmas');

    }

    // /**
    //  * Determine whether the User can restore the model.
    //  */
    // public function restore(User $user, Turma $turma): bool
    // {
    //     return false;
    // }

    // /**
    //  * Determine whether the User can permanently delete the model.
    //  */
    // public function forceDelete(User $user, Turma $turma): bool
    // {
    //     return false;
    // }
}

