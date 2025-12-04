<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('tasks.viewAny');
    }

    public function view(User $user, Task $task): bool
    {
        return $user->hasPermissionTo('tasks.view')
            && $task->organization_id === $user->organization_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('tasks.create');
    }

    public function update(User $user, Task $task): bool
    {
        return $user->hasPermissionTo('tasks.update')
            && $task->organization_id === $user->organization_id;
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->hasPermissionTo('tasks.delete')
            && $task->organization_id === $user->organization_id;
    }

    public function restore(User $user, Task $task): bool
    {
        return $user->hasPermissionTo('tasks.delete')
            && $task->organization_id === $user->organization_id;
    }

    public function forceDelete(User $user, Task $task): bool
    {
        return $user->hasPermissionTo('tasks.delete')
            && $task->organization_id === $user->organization_id;
    }
}
