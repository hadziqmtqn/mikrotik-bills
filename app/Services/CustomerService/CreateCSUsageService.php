<?php

namespace App\Services\CustomerService;

use App\Enums\PackageTypeService;
use App\Enums\StatusData;
use App\Models\CustomerService;
use App\Models\CustomerServiceUsage;
use App\Services\InvoiceSettingService;

class CreateCSUsageService
{
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
            $dailyPrice = $customerService->daily_price; // harga/tagihan harian

            //$nextBillingDate = $nextRepetitionDate->copy()->addMonth();

            /*if ($activeDate->isSameMonth($nextRepetitionDate) && $activeDate->lessThan($nextRepetitionDate)) {
                $diffInDays = $activeDate->diffInDays($nextRepetitionDate->copy()->addMonth());
            }else {
                $diffInDays = $activeDate->diffInDays($nextBillingDate);
            }*/

            $diffInDays = $activeDate
                ->copy()
                ->diffInDays($nextRepetitionDate);
            $totalPrice = $dailyPrice * $diffInDays;

            $customerServiceUsage = CustomerServiceUsage::query()
                ->lockForUpdate()
                ->firstOrNew(['invoice_id' => $invoiceId]);

            $customerServiceUsage->customer_service_id = $customerService->id;
            $customerServiceUsage->invoice_id = $invoiceId;
            $customerServiceUsage->used_since = $activeDate;
            $customerServiceUsage->next_billing_date = $nextRepetitionDate;
            $customerServiceUsage->days_of_usage = $diffInDays;
            $customerServiceUsage->daily_price = $dailyPrice;
            $customerServiceUsage->total_price = $totalPrice;
            $customerServiceUsage->save();
        }
    }
}
