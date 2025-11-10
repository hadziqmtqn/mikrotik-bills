<?php

namespace App\Observers;

use App\Models\CustomerService;
use App\Models\Invoice;
use App\Models\InvCustomerService;
use App\Traits\InvoiceSettingTrait;
use Illuminate\Support\Str;

class CustomerServiceObserver
{
    use InvoiceSettingTrait;

    public function creating(CustomerService $customerService): void
    {
        $customerService->slug = Str::uuid()->toString();
        $customerService->reference_number = 'CS-' . Str::upper(Str::random(8));
    }

    public function created(CustomerService $customerService): void
    {
        // TODO Create Invoice
        $invoice = new Invoice();
        $invoice->user_id = $customerService->user_id;
        $invoice->date = now();
        $invoice->due_date = now()->addDays($this->setting()?->due_date_after_new_service);
        $invoice->note = 'Dibuat secara otomatis oleh sistem';
        $invoice->save();

        // TODO Create Invoice Items
        $invoiceItem = new InvCustomerService();
        $invoiceItem->invoice_id = $invoice->id;
        $invoiceItem->customer_service_id = $customerService->id;
        $invoiceItem->amount = $customerService->price;
        $invoiceItem->save();
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
