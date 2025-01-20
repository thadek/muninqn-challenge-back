<?php

namespace App\Policies;

use App\Enums\Roles;
use Illuminate\Auth\Access\Response;
use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return auth()->check();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
       if($user->hasRole(Roles::ADMIN->value)){
           return true;
       }

        if ($user->tasks->contains($task->id)) {
            return true;
        }
       return false;
    }

    /**
     * Si es admin puede crear tareas, sino no.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(Roles::ADMIN->value);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        if($user->hasRole(Roles::ADMIN->value)){
            return true;
        }

        if($user->tasks->contains($task->id)){
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        return $user->hasRole(Roles::ADMIN->value);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        return false;
    }
}
