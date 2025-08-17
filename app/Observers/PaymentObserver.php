<?php

namespace App\Observers;

use App\Enums\StatusData;
use App\Models\Payment;
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
        if ($payment->status === StatusData::PAID->value) {
            $invoice = $payment->invoice;
            $invoice->status = StatusData::PAID->value;
            $invoice->save();
        }
    }

    public function updated(Payment $payment): void
    {
    }

    public function saved(Payment $payment): void
    {
    }

    public function deleted(Payment $payment): void
    {
    }

    public function restored(Payment $payment): void
    {
    }
}
