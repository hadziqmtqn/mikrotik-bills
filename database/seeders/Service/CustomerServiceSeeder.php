<?php

namespace Database\Seeders\Service;

use App\Enums\PaymentType;
use App\Enums\ServiceType;
use App\Models\CustomerService;
use App\Models\ExtraCost;
use App\Models\InvExtraCost;
use App\Models\Invoice;
use App\Models\InvCustomerService;
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

        $extraCosts = ExtraCost::pluck('fee', 'id');

        foreach ($users as $user) {
            $servicePackage = ServicePackage::where('plan_type', $user->userProfile?->account_type)
                ->inRandomOrder()
                ->first();

            if (!$servicePackage) continue;

            $isPpoe = $servicePackage->service_type === ServiceType::PPPOE->value;

            // TODO Customer Service
            /*$customerService = new CustomerService();
            $customerService->service_package_id = $servicePackage->id;
            $customerService->user_id = $user->id;
            $customerService->daily_price = $servicePackage->daily_price;
            $customerService->price = $servicePackage->package_price;
            $customerService->package_type = $isPpoe ? 'subscription' : 'one-time';
            $customerService->status = $faker->randomElement(['active', 'pending']);
            $customerService->save();*/
            $customerService = CreateCSService::handle(
                userId: $user->id,
                servicePackage: $servicePackage,
                packageType: ($isPpoe ? 'subscription' : 'one-time'),
                status: $faker->randomElement(['active', 'pending'])
            );

            // TODO Invoice
            $date = now()->subMonth();

            /*$invoice = Invoice::query()
                ->where('user_id', $user->id)
                ->firstOrNew();
            $invoice->user_id = $user->id;
            $invoice->date = $date;
            $invoice->due_date = $date->addDays(7);
            $invoice->status = $customerService->status == 'active' ? 'paid' : 'unpaid';
            $invoice->save();*/

            $invoice = CreateInvoiceService::handle(
                userId: $customerService->user_id,
                date: $date,
                dueDate: $date->addDays(7),
                defaultNote: 'Data dummy',
                defaultStatus: $customerService->status == 'active' ? 'paid' : 'unpaid'
            );

            // TODO Item Customer Service
            /*$invCustomerService = InvCustomerService::query()
                ->where('customer_service_id', $customerService->id)
                ->lockForUpdate()
                ->firstOrNew();
            $invCustomerService->invoice_id = $invoice->id;
            $invCustomerService->customer_service_id = $customerService->id;
            $invCustomerService->amount = $customerService->price;
            $invCustomerService->include_bill = $servicePackage->payment_type === PaymentType::PREPAID->value;
            $invCustomerService->save();*/
            CreateInvCSService::handle(
                invoiceId: $invoice->id,
                customerService: $customerService,
                includeBill: $servicePackage->payment_type === PaymentType::PREPAID->value
            );

            // TODO Extra Cost
            if ($isPpoe) {
                foreach ($extraCosts as $key => $extraCost) {
                    /*$invExtraCost = new InvExtraCost();
                    $invExtraCost->invoice_id = $invoice->id;
                    $invExtraCost->extra_cost_id = $key;
                    $invExtraCost->fee = $extraCost;
                    $invExtraCost->save();*/
                    CreateInvExtraCostService::handle(
                        invoiceId: $invoice->id,
                        extraCost: $extraCosts
                    );
                }
            }

            $invoice->refresh();

            RecalculateInvoiceTotalService::totalPrice($invoice);

            // TODO Payment
            if ($invoice->status == 'paid') {
                $datePaid = $invoice->date->addDays(2);

                $payment = new Payment();
                $payment->user_id = $user->id;
                $payment->invoice_id = $invoice->id;
                $payment->amount = $invoice->total_fee;
                $payment->payment_method = 'cash';
                $payment->date = $datePaid;
                $payment->status = 'paid';
                $payment->save();

                $customerService->start_date = $datePaid;
                $customerService->save();
            }
        }
    }
}
