<?php

namespace App\Services\CustomerService;

use App\Enums\PackageTypeService;
use App\Enums\PaymentType;
use App\Enums\StatusData;
use App\Models\CustomerService;
use Illuminate\Database\Eloquent\Builder;

class CSService
{
    /**
     * @param $userId
     * @param array|null $selfIds
     * @return array
     */
    public static function options($userId, array $selfIds = null): array
    {
        return CustomerService::query()
            ->with('servicePackage')
            ->whereHas('user', function (Builder $query) {
                $query->active();
                $query->whereNull('deleted_at');
            })
            ->where([
                'user_id' => $userId,
                'package_type' => PackageTypeService::SUBSCRIPTION->value
            ])
            ->where(function (Builder $query) {
                $query->whereDoesntHave('invCustomerServices.invoice', function (Builder $query) {
                    $query->whereMonth('date', now()->month)
                        ->whereYear('date', now()->year);
                });

                $query->orWhereHas('invCustomerServices.invoice.payments', function (Builder $query) {
                    $query->where('status', StatusData::PAID->value);
                    $query->whereDate('date', '<=', now()->subMonth()->lastOfMonth());
                });
            })
            ->when($selfIds, fn(Builder $query) => $query->whereIn('id', $selfIds))
            ->get()
            ->mapWithKeys(function (CustomerService $customerService) {
                $servicePackage = $customerService->servicePackage;
                $packageType = $customerService->package_type;

                return [$customerService->id => [
                    'name' => $servicePackage?->package_name,
                    'dailyPrice' => $customerService->daily_price,
                    // jika layanan baru (start_date masih kosong alias layan belum aktif), maka harga item menjadi 0
                    // jika sudah aktif tampilkan harga asli
                    'price' => !$customerService->start_date && $servicePackage?->payment_type === PaymentType::POSTPAID->value ? 0 : $customerService->price,
                    'packageType' => PackageTypeService::tryFrom($packageType)?->getLabel() ?? $packageType
                ]];
            })
            ->toArray();
    }

    public static function findById($id): ?CustomerService
    {
        return CustomerService::find($id);
    }
}
