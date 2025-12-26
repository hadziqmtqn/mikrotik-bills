<?php

namespace App\Services\CustomerService;

use App\Enums\BillingType;
use App\Enums\PackageTypeService;
use App\Enums\PaymentType;
use App\Models\AdditionalServiceFee;
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
            ->with([
                'servicePackage',
                'additionalServiceFees' => fn($query) => $query->where('is_active', true),
                'additionalServiceFees.extraCost'
            ])
            ->whereHas('user', function (Builder $query) {
                $query->active();
                $query->whereNull('deleted_at');
            })
            ->where('user_id', $userId)
            ->where(function (Builder $query) {
                // layanan berlangganan yang belum punya tagihan dibulan ini
                $query->whereDoesntHave('invCustomerServices.invoice', function (Builder $query) {
                    $query->whereMonth('date', now()->month)
                        ->whereYear('date', now()->year);
                });
            })
            ->when($selfIds, fn(Builder $query) => $query->whereIn('id', $selfIds))
            ->get()
            ->mapWithKeys(function (CustomerService $customerService) {
                $servicePackage = $customerService->servicePackage;
                $packageType = $customerService->package_type;

                // nominal tagihan tidak dibebankan
                $excludeBill = !$customerService->start_date && $servicePackage?->payment_type === PaymentType::POSTPAID->value;

                return [$customerService->id => [
                    'name' => $servicePackage?->package_name,
                    'dailyPrice' => $customerService->daily_price,
                    // jika layanan baru (start_date masih kosong alias layan belum aktif), maka harga item menjadi 0
                    // jika sudah aktif tampilkan harga asli
                    'price' => $excludeBill ? 0 : $customerService->price,
                    'includeBill' => !$excludeBill,
                    'packageType' => PackageTypeService::tryFrom($packageType)?->getLabel() ?? $packageType,
                    'additionalServiceFees' => self::additionalServiceFees($customerService)
                ]];
            })
            ->toArray();
    }

    public static function additionalServiceFees(CustomerService $customerService): array
    {
        $customerService->loadMissing('additionalServiceFees.extraCost');

        return $customerService->additionalServiceFees->filter(function (AdditionalServiceFee $fee) use ($customerService) {
            // 1. Jika start_date kosong → tampilkan semua
            if (is_null($customerService->start_date)) {
                return true;
            }

            // 2. Jika start_date terisi → hanya recurring
            return $fee->extraCost?->billing_type === BillingType::RECURRING->value;
        })
            ->map(function (AdditionalServiceFee $additionalServiceFee) {
                return [
                    'extra_cost_id' => $additionalServiceFee->extra_cost_id,
                    'name' => $additionalServiceFee->extraCost?->name,
                    'fee' => $additionalServiceFee->fee,
                ];
            })
            ->values()
            ->toArray();
    }

    public static function findById($id): ?CustomerService
    {
        return CustomerService::find($id);
    }
}
