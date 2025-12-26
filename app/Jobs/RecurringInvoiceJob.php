<?php

namespace App\Jobs;

use App\Models\CustomerServiceUsage;
use App\Services\CustomerService\CreateInvCSService;
use App\Services\CustomerService\CreateInvoiceService;
use App\Services\CustomerService\CSService;
use App\Traits\InvoiceSettingTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

class RecurringInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, InvoiceSettingTrait;

    protected CustomerServiceUsage $customerServiceUsage;

    /**
     * @param CustomerServiceUsage $customerServiceUsage
     */
    public function __construct(CustomerServiceUsage $customerServiceUsage)
    {
        $this->customerServiceUsage = $customerServiceUsage;
    }

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        DB::transaction(function () {
            $this->customerServiceUsage->loadMissing('customerService');
            $customerService = $this->customerServiceUsage->customerService;
            $now = Carbon::now();

            // TODO 1. Create Invoice
            $invoice = CreateInvoiceService::handle(
                userId: $customerService?->user_id,
                date: $now,
                dueDate: $now->copy()->addDays($this->setting()?->due_date_after_new_service),
                defaultNote: 'Dibuat otomatis oleh sistem'
            );

            // TODO 2. Create Invoice Customer Service
            CreateInvCSService::handle(
                invoiceId: $invoice->id,
                customerService: $customerService,
                includeBill: true,
                extraCosts: CSService::additionalServiceFees($customerService)
            );

            // TODO 3. Updating "inv_generated" on "Customer Service Usage" to "true"
            $this->customerServiceUsage
                ->update([
                    'inv_generated' => true
                ]);
        });
    }
}
