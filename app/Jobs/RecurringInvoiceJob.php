<?php

namespace App\Jobs;

use App\Enums\BillingType;
use App\Models\ExtraCost;
use App\Models\InvCustomerService;
use App\Models\InvExtraCost;
use App\Models\Invoice;
use App\Models\User;
use App\Traits\InvoiceSettingTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Throwable;

class RecurringInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, InvoiceSettingTrait;

    protected User $user;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        $extraCosts = ExtraCost::query()
            ->where('billing_type', BillingType::RECURRING->value)
            ->where('is_active', true)
            ->get();

        DB::transaction(function () use ($extraCosts) {
            $user = $this->user;

            $invoice = new Invoice();
            $invoice->user_id = $user->id;
            $invoice->date = now();
            $invoice->due_date = now()->addDays($this->setting()?->due_date_after_new_service);
            $invoice->note = 'Dibuat otomatis oleh sistem';
            $invoice->save();

            // Customer Serives
            foreach ($user->customerServices as $customerService) {
                $invCustomerService = new InvCustomerService();
                $invCustomerService->invoice_id = $invoice->id;
                $invCustomerService->customer_service_id = $customerService->id;
                $invCustomerService->amount = $customerService->price;
                $invCustomerService->save();
            }

            // Extra Cost
            foreach ($extraCosts as $extraCost) {
                $invExtraCost = new InvExtraCost();
                $invExtraCost->invoice_id = $invoice->id;
                $invExtraCost->extra_cost_id = $extraCost->id;
                $invExtraCost->fee = $extraCost->fee;
                $invExtraCost->save();
            }
        });
    }
}
