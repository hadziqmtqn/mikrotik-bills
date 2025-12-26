<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\CustomerService\CreateInvCSService;
use App\Services\CustomerService\CreateInvoiceService;
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
        DB::transaction(function () {
            $user = $this->user;

            $invoice = CreateInvoiceService::handle(
                userId: $user->id,
                date: now(),
                dueDate: now()->addDays($this->setting()?->due_date_after_new_service),
                defaultNote: 'Dibuat otomatis oleh sistem'
            );

            // Customer Serives
            foreach ($user->customerServices as $customerService) {
                CreateInvCSService::handle(
                    invoiceId: $invoice->id,
                    customerService: $customerService,
                    includeBill: true
                );
            }
        });
    }
}
