<?php

namespace App\Policies;

use App\Models\ExtraCost;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExtraCostPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_extra::cost');
    }

    public function view(User $user, ExtraCost $extraCost): bool
    {
        return $user->can('view_extra::cost', $extraCost);
    }

    public function create(User $user): bool
    {
        return $user->can('create_extra::cost');
    }

    public function update(User $user, ExtraCost $extraCost): bool
    {
        return $user->can('update_extra::cost', $extraCost);
    }

    public function delete(User $user, ExtraCost $extraCost): bool
    {
        $extraCost->loadCount('invExtraCosts');

        return $user->can('delete_extra::cost') && $extraCost->inv_extra_costs_count == 0;
    }
}
