<?php

namespace Database\Seeders\Service;

use App\Models\CustomerService;
use App\Models\Invoice;
use App\Models\InvoiceItem;
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

        $users = User::whereHas('roles', fn($query) => $query->where('name', 'user'))
            ->active()
            ->limit(40)
            ->get();

        $servicePackages = ServicePackage::get(['id', 'package_price']);

        foreach ($users as $user) {
            $servicePackageRandom = $servicePackages->random();

            // TODO Customer Service
            $customerService = new CustomerService();
            $customerService->service_package_id = $servicePackageRandom->id;
            $customerService->user_id = $user->id;
            $customerService->price = $servicePackageRandom->package_price;
            $customerService->package_type = $faker->randomElement(['subscription', 'one-time']);
            $customerService->status = $faker->randomElement(['active', 'pending']);
            $customerService->save();

            // TODO Invoice
            $invoice = Invoice::where('user_id', $user->id)
                ->firstOrNew();
            $invoice->user_id = $user->id;
            $invoice->date = now()->subDays(5);
            $invoice->due_date = now()->addDays(7);
            $invoice->status = $customerService->status == 'active' ? 'paid' : 'unpaid';
            $invoice->save();

            // TODO Invoice Items
            $invoiceItem = InvoiceItem::where('customer_service_id', $customerService->id)
                ->firstOrNew();
            $invoiceItem->invoice_id = $invoice->id;
            $invoiceItem->customer_service_id = $customerService->id;
            $invoiceItem->amount = $customerService->price;
            $invoiceItem->save();

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
