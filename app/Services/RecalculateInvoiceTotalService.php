<?php

namespace App\Services;

use App\Models\Invoice;

class RecalculateInvoiceTotalService
{
    /**
     * - Untuk pasang baru (pertama kali) jenis layanan PPoE dikenakan biaya pasang baru dan biaya tambahan lainnya (jika ada)
     * - PPoE jenis pembayaran postpaid (pasca bayar) tagihan paket internet dibebankan pada bulan berikutnya.
     * - PPoE jenis pembayaran prepaid (pra bayar) tagihan paket internet dibebenkan pada pasang baru
     */
    public static function totalPrice(Invoice $invoice): void
    {
        $invoice->refresh();
        $invoice->loadMissing('invCustomerServices.customerService.servicePackage');

        $totalCustomerServiceBill = 0;

        foreach ($invoice->invCustomerServices as $invCustomerService) {
            if ($invCustomerService->include_bill) {
                $totalCustomerServiceBill += $invCustomerService->amount;
            }
        }

        $invoice->updateQuietly([
            'total_price' => $totalCustomerServiceBill + $invoice->total_fee
        ]);
    }
}
