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
}
