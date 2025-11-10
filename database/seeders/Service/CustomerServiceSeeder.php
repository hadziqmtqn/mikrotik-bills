<?php

namespace Database\Seeders\Service;

use App\Models\CustomerService;
use App\Models\Invoice;
use App\Models\InvCustomerService;
use App\Models\Payment;
use App\Models\ServicePackage;
use App\Models\User;
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

        foreach ($users as $user) {
            $servicePackage = ServicePackage::where('plan_type', $user->userProfile?->account_type)
                ->inRandomOrder()
                ->first();

            if (!$servicePackage) continue;

            // TODO Customer Service
            $customerService = new CustomerService();
            $customerService->service_package_id = $servicePackage->id;
            $customerService->user_id = $user->id;
            $customerService->price = $servicePackage->package_price;
            $customerService->package_type = $faker->randomElement(['subscription', 'one-time']);
            $customerService->status = $faker->randomElement(['active', 'pending']);
            $customerService->save();

            // TODO Invoice
            $date = now()->subMonth();

            $invoice = Invoice::query()
                ->where('user_id', $user->id)
                ->firstOrNew();
            $invoice->user_id = $user->id;
            $invoice->date = $date;
            $invoice->due_date = $date->addDays(7);
            $invoice->status = $customerService->status == 'active' ? 'paid' : 'unpaid';
            $invoice->save();

            // TODO Invoice Items
            $invCustomerService = InvCustomerService::query()
                ->where('customer_service_id', $customerService->id)
                ->lockForUpdate()
                ->firstOrNew();
            $invCustomerService->invoice_id = $invoice->id;
            $invCustomerService->customer_service_id = $customerService->id;
            $invCustomerService->amount = $customerService->price;
            $invCustomerService->save();

            // TODO Payment
            if ($invoice->status == 'paid') {
                $payment = new Payment();
                $payment->user_id = $user->id;
                $payment->invoice_id = $invoice->id;
                $payment->amount = $customerService->price;
                $payment->payment_method = 'cash';
                $payment->date = $invoice->date->addDays(2);
                $payment->status = 'paid';
                $payment->save();
            }
        }
    }
}
