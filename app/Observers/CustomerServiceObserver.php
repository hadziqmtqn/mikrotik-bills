<?php

namespace App\Observers;

use App\Models\CustomerService;
use App\Models\Invoice;
use Illuminate\Support\Str;

class CustomerServiceObserver
{
    public function creating(CustomerService $customerService): void
    {
        $customerService->slug = Str::uuid()->toString();
        $customerService->reference_number = 'CS-' . Str::upper(Str::random(8));
    }

    public function forceDeleting(CustomerService $customerService): void
    {
        Invoice::query()
            ->whereHas('invCustomerServices', fn($query) => $query->where('customer_service_id', $customerService->id))
            ->delete();
    }
}
