<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdminPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_admin');
    }

    public function view(User $user, User $model): bool
    {
        return $user->can('view_admin', $model);
    }

    public function create(User $user): bool
    {
        return $user->can('create_admin');
    }

    public function update(User $user, User $model): bool
    {
        return $user->can('update_admin', $model);
    }

    public function delete(User $user, User $model): bool
    {
        return $user->can('delete_admin', $model);
    }

    public function restore(User $user, User $model): bool
    {
        return $user->can('restore_admin', $model);
    }

    public function forceDelete(User $user, User $model): bool
    {
        return $user->can('force_delete_admin', $model);
    }
}
