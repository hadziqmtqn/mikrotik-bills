<?php

namespace App\Observers;

use App\Models\CustomerService;
use Illuminate\Support\Str;

class CustomerServiceObserver
{
    public function creating(CustomerService $customerService): void
    {
        $customerService->slug = Str::uuid()->toString();
        $customerService->reference_number = 'CS-' . Str::upper(Str::random(8));
    }

    public function created(CustomerService $customerService): void
    {
    }

    public function updated(CustomerService $customerService): void
    {
    }

    public function deleted(CustomerService $customerService): void
    {
    }

    public function restored(CustomerService $customerService): void
    {
    }
}
