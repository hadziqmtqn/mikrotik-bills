<?php

namespace App\Policies;

use App\Models\InvoiceSetting;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvoiceSettingPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_invoice::setting');
    }

    public function update(User $user, InvoiceSetting $invoiceSetting): bool
    {
        return !$user->hasRole('user') && $user->can('update_invoice::setting', $invoiceSetting);
    }
}
