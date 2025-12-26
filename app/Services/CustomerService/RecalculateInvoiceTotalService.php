<?php

namespace App\Services\CustomerService;

use App\Enums\BillingType;
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
        $invoice->loadMissing([
            'invCustomerServices.customerService.additionalServiceFees' => function ($query) {
                $query->where('is_active', true);
            }
        ]);

        $totalPrice = 0;

        foreach ($invoice->invCustomerServices as $invCustomerService) {
            $customerService = $invCustomerService->customerService;

            $totalCustomerServiceBill = $invCustomerService->include_bill ? $invCustomerService->amount : 0;

            $extraFee = 0;
            if ($customerService?->additionalServiceFees->isNotEmpty()) {
                // BELUM mulai layanan → semua extra cost
                if (is_null($customerService->start_date)) {
                    $extraFee = $customerService->additionalServiceFees->sum('fee');
                }
                // SUDAH mulai layanan → hanya recurring
                else {
                    $extraFee = $customerService->additionalServiceFees
                        ->filter(fn ($fee) =>
                            $fee->extraCost?->billing_type === BillingType::RECURRING->value
                        )
                        ->sum('fee');
                }
            }

            $totalPrice += $totalCustomerServiceBill + $extraFee;
        }

        $invoice->updateQuietly([
            'total_price' => $totalPrice
        ]);
    }
}
