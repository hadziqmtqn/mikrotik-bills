<?php

namespace App\Services\CustomerService;

use App\Enums\PackageTypeService;
use App\Enums\StatusData;
use App\Models\CustomerService;
use App\Models\CustomerServiceUsage;
use App\Models\InvoiceSetting;
use Exception;
use Illuminate\Support\Carbon;

class CustomerServiceUsageService
{
    /**
     * @throws Exception
     */
    public static function handle(CustomerService $customerService, $invoiceId): void
    {
        $customerService->refresh();
        $customerService->loadMissing('customerServiceUsageLatest');

        if ($customerService->status === StatusData::ACTIVE->value &&
            $customerService->package_type === PackageTypeService::SUBSCRIPTION->value) {

            $invoiceSetting = InvoiceSetting::first();
            $billingDay = $invoiceSetting?->repeat_every_date ?? 5;

            $installationDate = $customerService->installation_date;
            if (!$installationDate) {
                throw new Exception('Installation date is required');
            }

            $installationDate = Carbon::parse($installationDate);
            $now = Carbon::now();

            // Tentukan periode mulai
            $lastPeriodEnd = $customerService->customerServiceUsageLatest?->period_end;
            $periodStart = $lastPeriodEnd
                ? Carbon::parse($lastPeriodEnd)
                : $installationDate->copy();

            // Tentukan tanggal billing bulan ini
            $currentBillingDate = Carbon::create($now->year, $now->month, $billingDay);

            // Logika untuk menentukan period_end dan next_billing_date
            if (!$lastPeriodEnd) {
                // LAYANAN BARU
                if ($installationDate->day <= $billingDay && $installationDate->isSameMonth($now)) {
                    // Instalasi sebelum atau pada tanggal billing bulan ini
                    // Invoice dibuat untuk bulan ini, period_end = tanggal 5 bulan ini
                    $periodEnd = $currentBillingDate->copy();
                    $nextBillingDate = $currentBillingDate->copy()->addMonthNoOverflow();
                } else {
                    // Instalasi setelah tanggal billing bulan ini atau bulan lalu
                    // Invoice dibuat untuk bulan depan, period_end = tanggal 5 bulan depan
                    $periodEnd = $currentBillingDate->copy()->addMonthNoOverflow();
                    $nextBillingDate = $periodEnd->copy()->addMonthNoOverflow();
                }
            } else {
                // LAYANAN LAMA (ada periode sebelumnya)
                // Hitung dari akhir periode terakhir sampai tanggal 5 bulan ini
                // Ini akan merangkum semua bulan yang terlewat dalam 1 invoice
                $periodEnd = $currentBillingDate->copy();
                $nextBillingDate = $currentBillingDate->copy()->addMonthNoOverflow();
            }

            // Hitung jumlah hari dan total harga
            $diffInDays = $periodStart->diffInDays($periodEnd);
            $dailyPrice = $customerService->daily_price;
            $totalPrice = $dailyPrice * $diffInDays;

            // Cek apakah sudah ada record untuk invoice ini
            $customerServiceUsage = CustomerServiceUsage::query()
                ->where([
                    'customer_service_id' => $customerService->id,
                    'invoice_id' => $invoiceId,
                ])
                ->first();

            if (!$customerServiceUsage) {
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
        }
    }

    public static function delete($invoiceId): void
    {
        CustomerServiceUsage::query()
            ->where('invoice_id', $invoiceId)
            ->delete();
    }
}
