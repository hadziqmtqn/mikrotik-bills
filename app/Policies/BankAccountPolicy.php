<?php

namespace App\Policies;

use App\Models\BankAccount;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BankAccountPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_bank::account');
    }

    public function view(User $user, BankAccount $bankAccount): bool
    {
        return $user->can('view_bank::account', $bankAccount);
    }

    public function create(User $user): bool
    {
        return $user->can('create_bank::account');
    }

    public function update(User $user, BankAccount $bankAccount): bool
    {
        return $user->can('update_bank::account', $bankAccount);
    }

    public function delete(User $user, BankAccount $bankAccount): bool
    {
        $bankAccount->loadCount('payments');

        return $user->can('delete_bank::account') && $bankAccount->payments_count === 0;
    }

    public function restore(User $user, BankAccount $bankAccount): bool
    {
        return $user->can('restore_bank::account', $bankAccount);
    }

    public function forceDelete(User $user, BankAccount $bankAccount): bool
    {
        return $user->can('force_delete_bank::account', $bankAccount);
    }
}
