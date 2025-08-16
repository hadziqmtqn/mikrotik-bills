<?php

namespace App\Observers;

use App\Models\Invoice;
use Illuminate\Support\Str;

class InvoiceObserver
{
    public function creating(Invoice $invoice): void
    {
        $invoice->slug = Str::uuid()->toString();
        $invoice->serial_number = Invoice::max('serial_number') + 1;
        $invoice->code = 'INV' . Str::padLeft($invoice->serial_number, 6, '0');
    }

    public function created(Invoice $invoice): void
    {
    }

    public function updated(Invoice $invoice): void
    {
    }

    public function deleted(Invoice $invoice): void
    {
    }

    public function restored(Invoice $invoice): void
    {
    }
}
