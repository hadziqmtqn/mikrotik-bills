<?php

namespace App\Services\CustomerService;

use App\Enums\PackageTypeService;
use App\Enums\PaymentType;
use App\Enums\StatusData;
use App\Models\CustomerService;
use App\Models\CustomerServiceUsage;
use App\Models\InvoiceSetting;
use App\Repositories\CustomerService\InvCustomerServiceRepository;
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
    public static function handle(CustomerService $customerService, $invoiceId): void
    {
        $customerService->refresh();
        $customerService->loadMissing([
            'customerServiceUsageLatest',
            'servicePackage'
        ]);

        if ($customerService->status !== StatusData::ACTIVE->value ||
            $customerService->package_type !== PackageTypeService::SUBSCRIPTION->value) {
            return;
        }

        $invoiceSetting = InvoiceSetting::first();
        $billingDay = $invoiceSetting?->repeat_every_date ?? 5;

        $installationDate = $customerService->installation_date;
        if (!$installationDate) {
            throw new Exception('Installation date is required');
        }

        $installationDate = Carbon::parse($installationDate);
        $now = Carbon::now();
        $currentBillingDate = Carbon::create($now->year, $now->month, $billingDay);

        // Tentukan periode mulai
        $lastUsage = $customerService->customerServiceUsageLatest;
        $lastPeriodEnd = $lastUsage?->period_end;
        $nextBillingDate = $currentBillingDate->copy()->addMonthNoOverflow();

        if (!$lastPeriodEnd) {
            // LAYANAN BARU - Periode mulai dari installation_date
            $periodStart = $installationDate->copy();
            $periodEnd = $installationDate;
        } else {
            // LAYANAN LAMA - Periode mulai dari akhir periode terakhir
            $periodStart = Carbon::parse($lastPeriodEnd);
            $periodEnd = $currentBillingDate->copy();
        }

        // Hitung jumlah hari dan total harga
        $diffInDays = $periodStart->diffInDays($periodEnd);
        $dailyPrice = $customerService->daily_price;

        if ($customerService->servicePackage?->payment_type === PaymentType::POSTPAID->value) {
            $totalPrice = $dailyPrice * $diffInDays;
        }else {
            $totalPrice = InvCustomerServiceRepository::totalBilling(customerServiceId: $customerService->id, invoiceId: $invoiceId);
        }


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

    public static function lastUsagePeriod(array $customerServiceIds): Carbon
    {
        $lastPeriod = CustomerServiceUsage::query()
            ->whereIn('customer_service_id', $customerServiceIds)
            ->orderByDesc('period_end')
            ->value('period_end');

        return $lastPeriod ? Carbon::parse($lastPeriod)->addDay() : Carbon::now();
    }
}
