<?php

namespace App\Policies;

use App\Enums\StatusData;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvoicePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_invoice');
    }

    public function view(User $user, Invoice $invoice): bool
    {
        return $user->can('view_invoice', $invoice);
    }

    public function create(User $user): bool
    {
        return $user->can('create_invoice');
    }

    public function update(User $user, Invoice $invoice): bool
    {
        if ($invoice->status !== StatusData::UNPAID->value) return false;

        return $user->can('update_invoice', $invoice);
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        $invoice->loadCount('payments');

        return $user->can('delete_invoice') && $invoice->payments_count === 0;
    }
}
