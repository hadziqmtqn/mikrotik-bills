<?php

namespace App\Services\CustomerService;

use App\Enums\PackageTypeService;
use App\Enums\PaymentType;
use App\Enums\StatusData;
use App\Models\CustomerService;
use App\Models\CustomerServiceUsage;
use App\Models\Invoice;
use App\Models\InvoiceSetting;
use Exception;
use Illuminate\Support\Carbon;

class CustomerServiceUsageService
{
    /**
     * Handle pembuatan usage untuk invoice
     *
     * PENTING: Method ini HANYA dipanggil untuk invoice SUBSCRIPTION,
     * TIDAK untuk invoice instalasi
     *
     * @throws Exception
     */
    public static function handle(CustomerService $customerService, Invoice $invoice): void
    {
        $customerService->refresh();
        $customerService->loadMissing([
            'customerServiceUsageLatest',
            'servicePackage'
        ]);

        /**
         * Abaikan jika layanan pelanggan tidak aktif dan bukan berlangganan
        */
        if ($customerService->status !== StatusData::ACTIVE->value ||
            $customerService->package_type !== PackageTypeService::SUBSCRIPTION->value) {
            return;
        }

        $invoice->refresh();
        $invoiceId = $invoice->id;
        $invoiceSetting = InvoiceSetting::first();
        $billingDay = $invoiceSetting?->repeat_every_date ?? 5;
        $servicePackage = $customerService->servicePackage;
        $paymentType = $servicePackage?->payment_type;
        $validityPeriod = $servicePackage?->validity_period;

        // Tanggal tagihan lunas pertama kali
        $invoicePaidDate = Carbon::parse($invoice->date);
        $now = Carbon::now();
        $currentBillingDate = Carbon::create($now->year, $now->month, $billingDay);

        // Tentukan periode mulai
        $lastUsage = $customerService->customerServiceUsageLatest;
        $lastPeriodEnd = $lastUsage?->period_end;

        if (!$lastPeriodEnd) {
            // LAYANAN BARU - Periode mulai dari installation_date
            $periodStart = $invoicePaidDate->copy();
            $periodEnd = $periodStart;
        } else {
            // LAYANAN LAMA - Periode mulai dari akhir periode terakhir
            $periodStart = Carbon::parse($lastPeriodEnd);
            $periodEnd = $currentBillingDate->copy();
        }

        /**
         * #### PREPAID (PRA BAYAR)
         * - Tanggal tagihan berikutnya langsung ke beberapa bulan sejak pelunasan
         * - Misal, tagihan lunas tanggal 23 Nov 2025 dan periode paket (validity_period) 1 bulan, maka tagihan berikutnya 23 Des 2025
         * ---
         *  #### POSTPAID (PASCA BAYAR)
         * - Jika tanggal pemasangan dibulan kemarin, maka tanggal "next_billing_date" bulan sekarang
         * - Jika pasang bulan ini, tanggal "next_billing_date" bulan depan
         * ---
         * - Pada tagihan dibulan yang akan datang "nex_billing_date" = period_end + validity_period
         * - Misal, periode terakhir tgl 05 Januari 2026 + 1 bulan = 05 Februari 2026
        */

        $lastMonth = $now->copy()->subMonth();

        // Hitung jumlah hari dan total harga
        $dailyPrice = $customerService->daily_price;

        if ($paymentType === PaymentType::PREPAID->value) {
            $nextBillingDate = $periodEnd->copy()->addMonths($validityPeriod);
        }else {
            // pembayaran bulan lalu
            if ($invoicePaidDate->year === $lastMonth->year && $invoicePaidDate->month === $lastMonth->month) {
                $nextBillingDate = $currentBillingDate->copy();
            }else {
                // pembayaran bulan sekarang
                if (!$lastUsage) {
                    $nextBillingDate = $currentBillingDate->copy()->addMonthNoOverflow();
                }else {
                    $nextBillingDate = $periodEnd->copy()->addMonths($validityPeriod);
                }
            }
        }

        if (!$lastUsage) {
            if ($paymentType === PaymentType::PREPAID->value) {
                $diffInDays = (int) $periodEnd->copy()->diffInDays($nextBillingDate->copy());
            }else {
                $diffInDays = (int) $periodStart->copy()->diffInDays($periodEnd->copy());
            }
        }else {
            $diffInDays = (int) $periodEnd->copy()->diffInDays($nextBillingDate->copy());
        }

        $totalPrice = $dailyPrice * $diffInDays;

        // Cek apakah sudah ada record untuk invoice ini
        $existingUsage = CustomerServiceUsage::query()
            ->where([
                'customer_service_id' => $customerService->id,
                'invoice_id' => $invoiceId,
            ])
            ->first();

        if (!$existingUsage) {
            CustomerServiceUsage::create([
                'customer_service_id' => $customerService->id,
                'invoice_id' => $invoiceId,
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'next_billing_date' => $nextBillingDate,
                'days_of_usage' => $diffInDays,
                'daily_price' => $dailyPrice,
                'total_price' => $totalPrice,
            ]);
        }

        CustomerServiceUsage::where('customer_service_id', $customerService->id)
            ->where('invoice_id', '!=', $invoiceId)
            ->update(['mark_done' => true]);
    }

    /**
     * Delete usage berdasarkan invoice_id
     */
    public static function delete($invoiceId): void
    {
        CustomerServiceUsage::query()
            ->where('invoice_id', $invoiceId)
            ->delete();
    }

    public static function lastUsagePeriod(array $customerServiceIds): string
    {
        $lastPeriod = CustomerServiceUsage::query()
            ->whereIn('customer_service_id', $customerServiceIds)
            ->orderByDesc('period_end')
            ->value('period_end');

        return $lastPeriod ? Carbon::parse($lastPeriod)->addDay()->toDateString() : Carbon::now()->toDateString();
    }
}
