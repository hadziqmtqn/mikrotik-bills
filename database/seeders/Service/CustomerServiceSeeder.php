<?php

namespace Database\Seeders\Service;

use App\Enums\PackageTypeService;
use App\Enums\PaymentType;
use App\Enums\ServiceType;
use App\Enums\StatusData;
use App\Models\CustomerServiceUsage;
use App\Models\ExtraCost;
use App\Models\InvoiceSetting;
use App\Models\Payment;
use App\Models\ServicePackage;
use App\Models\User;
use App\Services\CustomerService\CreateCSService;
use App\Services\CustomerService\CreateInvCSService;
use App\Services\CustomerService\CreateInvExtraCostService;
use App\Services\CustomerService\CreateInvoiceService;
use App\Services\RecalculateInvoiceTotalService;
use Faker\Factory;
use Illuminate\Database\Seeder;

class CustomerServiceSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Factory::create();

        $users = User::query()
            ->with('userProfile')
            ->whereHas('roles', fn($query) => $query->where('name', 'user'))
            ->active()
            ->limit(40)
            ->get();

        $extraCosts = ExtraCost::all();

        foreach ($users as $user) {
            $servicePackage = ServicePackage::where('plan_type', $user->userProfile?->account_type)
                ->inRandomOrder()
                ->first();

            if (!$servicePackage) continue;

            $isPpoe = $servicePackage->service_type === ServiceType::PPPOE->value;
            $date = now()->subMonths(2);

            // TODO Customer Service
            $customerService = CreateCSService::handle(
                userId: $user->id,
                servicePackage: $servicePackage,
                packageType: ($isPpoe ? 'subscription' : 'one-time'),
                installationDate: ($isPpoe ? $date : null),
                status: $faker->randomElement(['active', 'pending'])
            );

            // TODO Invoice
            $invoice = CreateInvoiceService::handle(
                userId: $customerService->user_id,
                date: $date,
                dueDate: $date->copy()->addDays(7),
                defaultNote: 'Data dummy',
                defaultStatus: $customerService->status == 'active' ? 'paid' : 'unpaid'
            );

            // TODO Item Customer Service
            CreateInvCSService::handle(
                invoiceId: $invoice->id,
                customerService: $customerService,
                includeBill: $servicePackage->payment_type === PaymentType::PREPAID->value
            );

            // TODO Extra Cost
            if ($isPpoe) {
                foreach ($extraCosts as $extraCost) {
                    CreateInvExtraCostService::handle(
                        invoiceId: $invoice->id,
                        extraCost: $extraCost
                    );
                }
            }

            $invoice->refresh();

            RecalculateInvoiceTotalService::totalPrice($invoice);

            // TODO Payment
            if ($invoice->status == 'paid') {
                $datePaid = $invoice->date
                    ->copy()
                    ->addDays(2);

                $payment = new Payment();
                $payment->user_id = $user->id;
                $payment->invoice_id = $invoice->id;
                $payment->amount = $invoice->total_price;
                $payment->payment_method = 'cash';
                $payment->date = $datePaid;
                $payment->status = 'paid';
                $payment->save();

                $customerService->start_date = $datePaid;
                $customerService->save();
                $customerService->refresh();

                // TODO Create Customer Service Usage
                if ($customerService->status === StatusData::ACTIVE->value && $customerService->package_type === PackageTypeService::SUBSCRIPTION->value) {
                    $invoiceSetting = InvoiceSetting::first();

                    $activeDate = $customerService->start_date; // mulai aktif digunakan
                    $dailyPrice = $customerService->daily_price; // harga/tagihan harian

                    $nextRepetitionDate = $activeDate->copy()
                        ->addMonthNoOverflow()
                        ->day($invoiceSetting->repeat_every_date);

                    $diffInDays = $activeDate->diffInDays($nextRepetitionDate);

                    $totalPrice = $dailyPrice * $diffInDays;

                    $customerServiceUsage = new CustomerServiceUsage();
                    $customerServiceUsage->customer_service_id = $customerService->id;
                    $customerServiceUsage->invoice_id = $invoice->id;
                    $customerServiceUsage->used_since = $activeDate;
                    $customerServiceUsage->next_billing_date = $nextRepetitionDate;
                    $customerServiceUsage->days_of_usage = $diffInDays;
                    $customerServiceUsage->daily_price = $dailyPrice;
                    $customerServiceUsage->total_price = $totalPrice;
                    $customerServiceUsage->save();
                }
            }
        }
    }
}
