<?php

namespace App\Services\CustomerService;

use App\Enums\PackageTypeService;
use App\Enums\StatusData;
use App\Models\CustomerService;
use App\Models\CustomerServiceUsage;
use App\Services\InvoiceSettingService;

class CreateCSUsageService
{
    public static function handle(CustomerService $customerService): void
    {
        $customerService->refresh();

        if ($customerService->status === StatusData::ACTIVE->value && $customerService->package_type === PackageTypeService::SUBSCRIPTION->value) {
            /**
             * - Jika layanan aktif pada bulan kemarin dan jadwal pembuatan ulang tagihan otmatis pada bulan ini maka total tagihan terhitung sampai bulan ini
             * - Jika layanan aktif pada bulan yang sama dengan jadwal pembuatan ulang tagihan, maka total tagihan sampai bulan dengan
            */
            $nextRepetitionDate = InvoiceSettingService::nextRepetitionDate();

            $activeDate = $customerService->start_date; // mulai aktif digunakan
            $dailyPrice = $customerService->daily_price; // harga/tagihan harian

            if ($activeDate->isSameMonth($nextRepetitionDate->copy()->subMonth())) {
                $diffInDays = $activeDate->diffInDays($nextRepetitionDate);
                $nextBillingDate = $nextRepetitionDate;
            }else {
                $nextBillingDate = $nextRepetitionDate->addMonth();
                $diffInDays = $activeDate->diffInDays($nextBillingDate);
            }

            $totalPrice = $dailyPrice * $diffInDays;

            $customerServiceUsage = new CustomerServiceUsage();
            $customerServiceUsage->customer_service_id = $customerService->id;
            $customerServiceUsage->used_since = $activeDate;
            $customerServiceUsage->next_billing_date = $nextBillingDate;
            $customerServiceUsage->days_of_usage = $diffInDays;
            $customerServiceUsage->daily_price = $dailyPrice;
            $customerServiceUsage->total_price = $totalPrice;
            $customerServiceUsage->save();
        }
    }
}
