<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_user');
    }

    public function view(User $user, User $model): bool
    {
        return $user->can('view_user') || $user->id === $model->id;
    }

    public function create(User $user): bool
    {
        return $user->can('create_user');
    }

    public function update(User $user, User $model): bool
    {
        return $user->can('update_user') || $user->id === $model->id;
    }

    public function delete(User $user, User $model): bool
    {
        $model->loadCount('invoices')
            ->loadCount('customerServices');

        return $user->can('delete_user') && $model->invoices_count === 0 && $model->customer_services_count === 0;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }

    public function restore(User $user, User $model): bool
    {
        return $user->can('restore_user', $model);
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function forceDelete(User $user, User $model): bool
    {
        return $user->hasRole('super_admin', $model);
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }
}
