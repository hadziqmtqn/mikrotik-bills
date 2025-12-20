<?php

namespace App\Policies;

use App\Enums\StatusData;
use App\Models\CustomerService;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerServicePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_customer::service');
    }

    public function view(User $user, CustomerService $customerService): bool
    {
        return $user->can('view_customer::service', $customerService);
    }

    public function create(User $user): bool
    {
        return $user->can('create_customer::service');
    }

    public function update(User $user, CustomerService $customerService): bool
    {
        return $user->can('update_customer::service', $customerService);
    }

    public function delete(User $user, CustomerService $customerService): bool
    {
        return $user->can('delete_customer::service') && $customerService->status === StatusData::PENDING->value;
    }

    public function restore(User $user, CustomerService $customerService): bool
    {
        return $user->can('restore_customer::service', $customerService);
    }

    public function forceDelete(User $user, CustomerService $customerService): bool
    {
        return $user->can('force_delete_customer::service', $customerService);
    }
}
