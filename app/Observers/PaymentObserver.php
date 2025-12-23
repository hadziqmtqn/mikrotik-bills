<?php

namespace App\Observers;

use App\Enums\StatusData;
use App\Models\Payment;
use App\Services\CustomerService\CustomerServiceUsageService;
use Exception;
use Illuminate\Support\Str;

class PaymentObserver
{
    public function creating(Payment $payment): void
    {
        $payment->slug = Str::uuid()->toString();
        $payment->serial_number = Payment::max('serial_number') + 1;
        $payment->code = 'PAY' . Str::padLeft($payment->serial_number, 6, '0');
    }

    public function created(Payment $payment): void
    {
        $payment->loadMissing('invoice');

        if ($payment->status === StatusData::PAID->value) {
            $invoice = $payment->invoice;
            $invoice->status = StatusData::PAID->value;
            $invoice->save();
        }
    }

    /**
     * @throws Exception
     */
    public function updating(Payment $payment): void
    {
        $payment->loadMissing('invoice.invCustomerServices');
        $invoice = $payment->invoice;

        // Ubah status Invoice menjadi UNPAID jika pembayaran dibatalkan
        if ($payment->status === StatusData::CANCELLED->value) {
            CustomerServiceUsageService::delete($payment->invoice_id);

            $invoice->update(['status' => StatusData::UNPAID->value]);
        }

        // Ubah status Invoice menjadi PAID jika pembayaran diubah menjadi status PAID
        if ($payment->status === StatusData::PAID->value) {
            $invoice->update(['status' => StatusData::PAID->value]);

            if ($invoice->invCustomerServices->isNotEmpty()) {
                foreach ($invoice->invCustomerServices as $invCustomerService) {
                    CustomerServiceUsageService::handle(
                        customerService: $invCustomerService->customerService,
                        invoiceId: $payment->invoice_id
                    );
                }
            }
        }
    }
}
