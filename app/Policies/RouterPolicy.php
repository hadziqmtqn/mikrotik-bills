<?php

namespace App\Policies;

use App\Models\Router;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RouterPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_router');
    }

    public function view(User $user, Router $router): bool
    {
        return $user->can('view_router', $router);
    }

    public function create(User $user): bool
    {
        return $user->can('create_router');
    }

    public function update(User $user, Router $router): bool
    {
        return $user->can('update_router', $router);
    }

    public function delete(User $user, Router $router): bool
    {
        $router->loadCount('servicePackages');

        return $user->can('delete_router') && $router->service_packages_count === 0;
    }
}
