<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public static function dropdownOptions($selfId = null): array
    {
        return User::with('userProfile')
            ->whereHas('userProfile')
            ->whereHas('roles', fn($query) => $query->where('name', 'user'))
            ->when($selfId, function ($query) use ($selfId) {
                return $query->where('id', $selfId);
            })
            ->orderBy('name')
            ->active()
            ->get()
            ->mapWithKeys(function (User $user) {
                return [$user->id => $user->name . ' (' . $user->userProfile?->ppoe_name . ')'];
            })
            ->toArray();
    }
}
