<?php

namespace App\Policies;

use App\Models\ServicePackage;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServicePackagePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_service::package');
    }

    public function view(User $user, ServicePackage $servicePackage): bool
    {
        return $user->can('view_service::package', $servicePackage);
    }

    public function create(User $user): bool
    {
        return $user->can('create_service::package');
    }

    public function update(User $user, ServicePackage $servicePackage): bool
    {
        return $user->can('update_service::package', $servicePackage);
    }

    public function delete(User $user, ServicePackage $servicePackage): bool
    {
        $servicePackage->loadMissing('customerServices');

        return $user->can('delete_service::package') && $servicePackage->customer_services_count === 0;
    }

    public function restore(User $user, ServicePackage $servicePackage): bool
    {
        return $user->can('restore_service::package', $servicePackage);
    }

    public function forceDelete(User $user, ServicePackage $servicePackage): bool
    {
        return $user->can('force_delete_service::package', $servicePackage);
    }
}
