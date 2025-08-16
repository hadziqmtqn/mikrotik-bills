<?php

namespace App\Policies;

use App\Models\PaymentSetting;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentSettingPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_payment::setting');
    }

    public function update(User $user, PaymentSetting $paymentSetting): bool
    {
        return !$user->hasRole('user') && $user->can('update_payment::setting', $paymentSetting);
    }
}
