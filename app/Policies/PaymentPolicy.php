<?php

namespace App\Policies;

use App\Enums\StatusData;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_payment');
    }

    public function view(User $user, Payment $payment): bool
    {
        return $user->can('view_payment', $payment);
    }

    public function create(User $user): bool
    {
        return $user->can('create_payment');
    }

    public function update(User $user, Payment $payment): bool
    {
        return $user->can('update_payment', $payment);
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $user->can('delete_payment') && $payment->status != StatusData::PAID->value;
    }

    public function restore(User $user, Payment $payment): bool
    {
        return $user->can('restore_payment', $payment);
    }

    public function forceDelete(User $user, Payment $payment): bool
    {
        return $user->can('force_delete_payment', $payment);
    }
}
