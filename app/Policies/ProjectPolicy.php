<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('projects.viewAny');
    }

    public function view(User $user, Project $project): bool
    {
        return $user->hasPermissionTo('projects.view')
            && $project->organization_id === $user->organization_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('projects.create');
    }

    public function update(User $user, Project $project): bool
    {
        return $user->hasPermissionTo('projects.update')
            && $project->organization_id === $user->organization_id;
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->hasPermissionTo('projects.delete')
            && $project->organization_id === $user->organization_id;
    }

    public function restore(User $user, Project $project): bool
    {
        return $user->hasPermissionTo('projects.delete')
            && $project->organization_id === $user->organization_id;
    }

    public function forceDelete(User $user, Project $project): bool
    {
        return $user->hasPermissionTo('projects.delete')
            && $project->organization_id === $user->organization_id;
    }
}
