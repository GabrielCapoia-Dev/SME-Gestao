<?php

namespace App\Policies;

use App\Models\Professor;
use App\Models\User;

class ProfessorPolicy
{
    /**
     * Determine whether the User can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('Listar Professores');
    }

    /**
     * Determine whether the User can view the model.
     */
    public function view(User $user, Professor $professor): bool
    {
        return $user->hasPermissionTo('Listar Professores');
    }

    /**
     * Determine whether the User can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('Criar Professores');

    }

    /**
     * Determine whether the User can update the model.
     */
    public function update(User $user, Professor $professor): bool
    {
        return $user->hasPermissionTo('Editar Professores');

    }

    /**
     * Determine whether the User can delete the model.
     */
    public function delete(User $user, Professor $professor): bool
    {
        return $user->hasPermissionTo('Excluir Professores');

    }

    // /**
    //  * Determine whether the User can restore the model.
    //  */
    // public function restore(User $user, Professor $professor): bool
    // {
    //     return false;
    // }

    // /**
    //  * Determine whether the User can permanently delete the model.
    //  */
    // public function forceDelete(User $user, Professor $professor): bool
    // {
    //     return false;
    // }
}

