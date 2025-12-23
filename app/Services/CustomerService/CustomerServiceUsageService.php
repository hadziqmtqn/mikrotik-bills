<?php

namespace App\Services\CustomerService;

use App\Enums\PackageTypeService;
use App\Enums\StatusData;
use App\Models\CustomerService;
use App\Models\CustomerServiceUsage;
use App\Services\InvoiceSettingService;
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

        if ($customerService->status === StatusData::ACTIVE->value && $customerService->package_type === PackageTypeService::SUBSCRIPTION->value) {
            /**
             * #### Ketentuan untuk pasang baru:
             * - Tanggal aktif sebelum jadwal perulangan dibulan yang sama, maka "next billing date" langsung ke bulan depan
             * - Jika aktif setelah jadwal perulangan dibulan yang sama, "next billing date" juga langsung ke bulan depan
             *
             * #### Ketentuan untuk layanan sudah berjalan lebih dari 1 bulan:
             * - Tanggal aktif pada penggunaan dimulai sejak terakhir digunakan, bukan sejak aktif
            */

            $nextRepetitionDate = InvoiceSettingService::nextRepetitionDate();

            $activeDate = $customerService->customerServiceUsageLatest?->next_billing_date ?? $customerService->installation_date; // mulai aktif digunakan atau tanggal pemasangan

            if (!$activeDate) {
                throw new Exception('Active date (installation_date or next_billing_date) is required');
            }

            // Pastikan dalam bentuk Carbon instance
            $activeDate = Carbon::parse($activeDate);
            $nextRepetitionDate = Carbon::parse($nextRepetitionDate);

            $dailyPrice = $customerService->daily_price; // harga/tagihan harian

            $diffInDays = $activeDate
                ->copy()
                ->diffInDays($nextRepetitionDate);

            $daysOfusage = (int) $diffInDays;
            $totalPrice = $dailyPrice * $daysOfusage;

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
                    'used_since' => $activeDate,
                    'next_billing_date' => $nextRepetitionDate,
                    'days_of_usage' => $daysOfusage,
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
