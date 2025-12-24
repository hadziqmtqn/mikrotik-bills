<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UserService
{
    public static function options($selfId = null, $accountType = null, $onlyHasServices = false): array
    {
        return User::with('userProfile')
            ->whereHas('userProfile', function (Builder $query) use ($accountType) {
                $query->when($accountType, fn(Builder $query) => $query->where('account_type', $accountType));
            })
            ->whereHas('roles', fn(Builder $query) => $query->where('name', 'user'))
            ->when($selfId, function (Builder $query) use ($selfId) {
                return $query->where('id', $selfId);
            })
            ->when($onlyHasServices, fn(Builder $query) => $query->whereHas('customerServices'))
            ->orderBy('name')
            ->active()
            ->get()
            ->mapWithKeys(function (User $user) {
                return [$user->id => $user->name . ' (' . $user->userProfile?->ppoe_name . ')'];
            })
            ->toArray();
    }
}
