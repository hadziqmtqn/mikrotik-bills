<?php

namespace App\Console\Commands;

use App\Enums\PackageTypeService;
use App\Enums\StatusData;
use App\Enums\TimeLimitType;
use App\Jobs\RecurringInvoiceJob;
use App\Models\InvoiceSetting;
use App\Models\User;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class MakeRecurringInvoiceCommand extends Command
{
    protected $signature = 'make:recurring-invoice';

    protected $description = 'Command description';

    public function handle(): void
    {
        Log::info('make:recurring-invoice started');

        try {
            $invoiceSetting = InvoiceSetting::first();

            if ($invoiceSetting?->setup_auto_recurring_invoice && (Carbon::now()->day >= $invoiceSetting->repeat_every_date)) {
                $users = User::query()
                    ->with([
                        'customerServices' => function ($query) {
                            $query->where('status', StatusData::ACTIVE->value);
                            $query->where('package_type', PackageTypeService::SUBSCRIPTION->value);
                            $query->whereHas('servicePackage', function ($query) {
                                $query->where('validity_unit', TimeLimitType::BULAN->value);
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
                    ->whereHas('roles', fn($query) => $query->where('name', 'user'))
                    ->whereHas('customerServices', function ($query) {
                        $query->where('status', StatusData::ACTIVE->value);
                        $query->where('package_type', PackageTypeService::SUBSCRIPTION->value);
                        $query->whereHas('servicePackage', function ($query) {
                            $query->where('validity_unit', TimeLimitType::BULAN->value);
                        });
                        $query->whereDoesntHave('invCustomerServices.invoice', function ($query) {
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

                Log::info('Total ' . $users->count() . ' New Invoice(s)');

                foreach ($users as $user) {
                    RecurringInvoiceJob::dispatch($user);
                }
            }
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
        }
    }
}
