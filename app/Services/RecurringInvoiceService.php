<?php

namespace App\Services;

use App\Enums\PackageTypeService;
use App\Enums\StatusData;
use App\Enums\ValidityUnit;
use App\Jobs\RecurringInvoiceJob;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class RecurringInvoiceService
{
    public function handle(): void
    {
        $users = User::query()
            ->with([
                'customerServices' => function (Builder $query) {
                    $query->where('status', StatusData::ACTIVE->value);
                    $query->where('package_type', PackageTypeService::SUBSCRIPTION->value);
                    $query->whereHas('servicePackage', function (Builder $query) {
                        $query->where('validity_unit', ValidityUnit::BULAN->value);
                    });
                    $query->whereDoesntHave('invCustomerServices.invoice', function ($query) {
                        $query->whereMonth('date', now()->month)
                            ->whereYear('date', now()->year)
                            ->whereIn('status', [
                                StatusData::UNPAID->value,
                                StatusData::PAID->value
                            ]);
                    });
                },
                'customerServices.servicePackage',
                'customerServices.invCustomerServices.invoice',
            ])
            ->whereHas('roles', fn(Builder $query) => $query->where('name', 'user'))
            ->whereHas('customerServices', function (Builder $query) {
                $query->where('status', StatusData::ACTIVE->value);
                $query->where('package_type', PackageTypeService::SUBSCRIPTION->value);
                $query->whereHas('servicePackage', function (Builder $query) {
                    $query->where('validity_unit', ValidityUnit::BULAN->value);
                });
                $query->whereDoesntHave('invCustomerServices.invoice', function (Builder $query) {
                    $query->whereMonth('date', now()->month)
                        ->whereYear('date', now()->year)
                        ->whereIn('status', [
                            StatusData::UNPAID->value,
                            StatusData::PAID->value
                        ]);
                });
            })
            ->active()
            ->get();


        foreach ($users as $user) {
            RecurringInvoiceJob::dispatch($user);
        }
    }
}
