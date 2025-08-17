<?php

namespace App\Observers;

use App\Enums\StatusData;
use App\Models\CustomerService;
use App\Models\Invoice;
use App\Traits\InvoiceSettingTrait;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class InvoiceObserver
{
    use InvoiceSettingTrait;

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
        $invoice->refresh();

        if ($invoice->status === StatusData::OVERDUE->value) {
            $invoice->cancel_date = Carbon::parse($invoice->due_date)->addDays($this->setting()?->cancel_after ?? 7);
        }

        if ($invoice->status === StatusData::CANCELLED->value) {
            CustomerService::whereHas('invoiceItems', fn($query) => $query->where('invoice_id', $invoice->id))
                ->update([
                    'status' => StatusData::CANCELLED->value,
                    'notes' => 'Layanan pelanggan dibatalkan otomatis karena tagihan tidak dibayar setelah tanggal jatuh tempo.',
                ]);
        }

        $invoice->save();
    }

    public function deleted(Invoice $invoice): void
    {
    }

    public function restored(Invoice $invoice): void
    {
    }
}
