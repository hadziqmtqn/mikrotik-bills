<?php

namespace App\Console\Commands;

use App\Enums\StatusData;
use App\Jobs\RecurringInvoiceJob;
use App\Models\CustomerServiceUsage;
use App\Models\InvoiceSetting;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MakeRecurringInvoiceCommand extends Command
{
    protected $signature = 'make:recurring-invoice';

    protected $description = 'Command description';

    public function handle(): void
    {
        Log::info('make:recurring-invoice started');

        try {
            $invoiceSetting = InvoiceSetting::first();

            if ($invoiceSetting?->setup_auto_recurring_invoice) {
                $customerServiceUsages = CustomerServiceUsage::query()
                    ->whereHas('customerService', fn($query) => $query->where('status', StatusData::ACTIVE->value))
                    ->where('next_billing_date', '<=', now())
                    ->where([
                        'mark_done' => false,
                        'inv_generated' => false
                    ])
                    ->get();

                foreach ($customerServiceUsages as $customerServiceUsage) {
                    RecurringInvoiceJob::dispatch($customerServiceUsage);
                }
            }
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
        }
    }
}
