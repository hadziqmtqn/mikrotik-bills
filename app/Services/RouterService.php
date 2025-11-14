<?php

namespace App\Services;

use App\Models\Router;
use Illuminate\Database\Eloquent\Builder;

class RouterService
{
    public static function options($selfId = null): array
    {
        return Router::query()
            ->where('is_active', true)
            ->orWhere(function (Builder $query) use ($selfId) {
                $query->when($selfId, fn(Builder $query) => $query->where('id', $selfId));
            })
            ->get()
            ->mapWithKeys(function (Router $router) {
                return [$router->id => $router->name . ' (' . $router->ip_address . ')'];
            })
            ->toArray();
    }
}
