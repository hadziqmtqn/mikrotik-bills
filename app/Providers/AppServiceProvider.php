<?php

namespace App\Providers;

use App\Models\CustomerService;
use App\Models\InvExtraCost;
use App\Models\Invoice;
use App\Models\Payment;
use App\Observers\CustomerServiceObserver;
use App\Observers\InvExtraCostObserver;
use App\Observers\InvoiceObserver;
use App\Observers\PaymentObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        CustomerService::observe(CustomerServiceObserver::class);
        InvExtraCost::observe(InvExtraCostObserver::class);
        Invoice::observe(InvoiceObserver::class);
        Payment::observe(PaymentObserver::class);
    }
}
