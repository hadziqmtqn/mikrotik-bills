<?php

namespace App\Observers;

use App\Enums\StatusData;
use App\Models\Invoice;
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

    /**
     * @throws Exception
     */
    public function created(Payment $payment): void
    {
        $payment->refresh();
        $payment->loadMissing('invoice');

        if ($payment->status === StatusData::PAID->value) {
            $this->updateUsage($payment->invoice, $payment->date);
        }
    }

    /**
     * @throws Exception
     */
    public function updated(Payment $payment): void
    {
        $payment->refresh();
        $payment->loadMissing('invoice.invCustomerServices');
        $invoice = $payment->invoice;

        // Ubah status Invoice menjadi UNPAID jika pembayaran dibatalkan
        if ($payment->status === StatusData::CANCELLED->value) {
            CustomerServiceUsageService::delete($payment->invoice_id);

            $invoice->update(['status' => StatusData::UNPAID->value]);
        }

        if ($payment->status === StatusData::PAID->value) {
            $this->updateUsage($invoice, $payment->date);
        }
    }

    /**
     * @throws Exception
     */
    private function updateUsage(Invoice $invoice, $payDate): void
    {
        $invoice->refresh();
        $invoice->loadMissing('invCustomerServices');

        // Ubah status Invoice menjadi PAID jika pembayaran diubah menjadi status PAID
        $invoice->update(['status' => StatusData::PAID->value]);

        if ($invoice->invCustomerServices->isNotEmpty()) {
            foreach ($invoice->invCustomerServices as $invCustomerService) {
                $customerService = $invCustomerService->customerService;

                $customerService->update([
                    'status' => StatusData::ACTIVE->value,
                    'start_date' => $payDate
                ]);

                CustomerServiceUsageService::handle(
                    customerService: $invCustomerService->customerService,
                    invoice: $invoice
                );
            }
        }
    }
}
