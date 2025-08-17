<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;

class InvoiceOverDueCommand extends Command
{
    protected $signature = 'invoice:over-due';

    protected $description = 'Ubah status invoice yang sudah lewat jatuh tempo menjadi overdue';

    public function handle(): void
    {
        Invoice::where('due_date', '<', now()->toDateTimeString())
            ->where('status', 'unpaid')
            ->get()
            ->each(function ($invoice) {
                $invoice->update(['status' => 'overdue']);
            });
    }
}
