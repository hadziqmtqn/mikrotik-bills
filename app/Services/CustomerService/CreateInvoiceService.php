<?php

namespace App\Services\CustomerService;

use App\Models\Invoice;

class CreateInvoiceService
{
    /**
     * @param $userId
     * @param $date
     * @param $dueDate
     * @param $defaultNote
     * @param $defaultStatus
     * @return Invoice
     */
    public static function handle($userId, $date, $dueDate, $defaultNote = null, $defaultStatus = null): Invoice
    {
        $invoice = new Invoice();
        $invoice->user_id = $userId;
        $invoice->date = $date;
        $invoice->due_date = $dueDate;
        $invoice->note = $defaultNote;

        if ($defaultStatus) {
            $invoice->status = $defaultStatus;
        }

        $invoice->save();

        return $invoice;
    }
}
