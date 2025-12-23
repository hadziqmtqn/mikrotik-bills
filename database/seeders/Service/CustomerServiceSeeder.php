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
use Carbon\Carbon;
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
        $invoiceSetting = InvoiceSetting::first();

        foreach ($users as $user) {
            $servicePackage = ServicePackage::where('plan_type', $user->userProfile?->account_type)
                ->inRandomOrder()
                ->first();

            if (!$servicePackage) continue;

            $isPpoe = $servicePackage->service_type === ServiceType::PPPOE->value;
            $installationDate = now()->subMonths(2); // Layanan dibuat 2 bulan lalu

            // Create Customer Service
            $customerService = CreateCSService::handle(
                userId: $user->id,
                servicePackage: $servicePackage,
                packageType: ($isPpoe ? 'subscription' : 'one-time'),
                installationDate: ($isPpoe ? $installationDate : null),
                status: $faker->randomElement(['active', 'pending'])
            );

            // Hitung berapa invoice yang perlu dibuat
            $billingPeriods = $this->calculateBillingPeriods(
                $installationDate,
                now(),
                $invoiceSetting->repeat_every_date
            );

            // Buat invoice untuk setiap periode
            foreach ($billingPeriods as $index => $period) {
                $invoice = CreateInvoiceService::handle(
                    userId: $customerService->user_id,
                    date: $period['invoice_date'],
                    dueDate: $period['invoice_date']->copy()->addDays(7),
                    defaultNote: "Tagihan periode {$period['period_name']}",
                    defaultStatus: $customerService->status == 'active' ? 'paid' : 'unpaid'
                );

                // Item Customer Service
                CreateInvCSService::handle(
                    invoiceId: $invoice->id,
                    customerService: $customerService,
                    includeBill: $servicePackage->payment_type === PaymentType::PREPAID->value
                );

                // Extra Cost untuk PPPoE
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

                // Payment jika status paid
                if ($invoice->status == 'paid') {
                    $datePaid = $period['invoice_date']->copy()->addDays(2);

                    $payment = new Payment();
                    $payment->user_id = $user->id;
                    $payment->invoice_id = $invoice->id;
                    $payment->amount = $invoice->total_price;
                    $payment->payment_method = 'cash';
                    $payment->date = $datePaid;
                    $payment->status = 'paid';
                    $payment->save();

                    // Set start_date hanya untuk invoice pertama
                    if ($index === 0) {
                        $customerService->start_date = $datePaid;
                        $customerService->save();
                        $customerService->refresh();
                    }

                    // Create Customer Service Usage
                    if ($customerService->status === StatusData::ACTIVE->value &&
                        $customerService->package_type === PackageTypeService::SUBSCRIPTION->value) {

                        $usedSince = $period['period_start'];
                        $nextBillingDate = $period['period_end'];
                        $diffInDays = $usedSince->diffInDays($nextBillingDate);
                        $dailyPrice = $customerService->daily_price;
                        $totalPrice = $dailyPrice * $diffInDays;

                        $customerServiceUsage = new CustomerServiceUsage();
                        $customerServiceUsage->customer_service_id = $customerService->id;
                        $customerServiceUsage->invoice_id = $invoice->id;
                        $customerServiceUsage->used_since = $usedSince;
                        $customerServiceUsage->next_billing_date = $nextBillingDate;
                        $customerServiceUsage->days_of_usage = $diffInDays;
                        $customerServiceUsage->daily_price = $dailyPrice;
                        $customerServiceUsage->total_price = $totalPrice;
                        $customerServiceUsage->save();
                    }
                }
            }
        }
    }

    /**
     * Hitung periode billing dari tanggal instalasi sampai sekarang
     *
     * @param Carbon $startDate Tanggal mulai layanan
     * @param Carbon $endDate Tanggal sekarang
     * @param int $billingDay Tanggal billing (misal: 5)
     * @return array
     */
    private function calculateBillingPeriods(Carbon $startDate, Carbon $endDate, int $billingDay): array
    {
        $periods = [];

        // Tentukan tanggal invoice pertama
        $currentInvoiceDate = $startDate->copy()->day($billingDay);

        // Jika start_date setelah billing day di bulan yang sama,
        // invoice pertama adalah bulan berikutnya
        if ($startDate->day > $billingDay) {
            $currentInvoiceDate->addMonthNoOverflow();
        }

        $periodStart = $startDate->copy();

        // Loop sampai invoice date melebihi tanggal sekarang
        while ($currentInvoiceDate->lte($endDate)) {
            $periodEnd = $currentInvoiceDate->copy();

            $periods[] = [
                'invoice_date' => $currentInvoiceDate->copy(),
                'period_start' => $periodStart->copy(),
                'period_end' => $periodEnd->copy(),
                'period_name' => $currentInvoiceDate->locale('id')->translatedFormat('F Y'),
            ];

            // Pindah ke periode berikutnya
            $periodStart = $periodEnd->copy();
            $currentInvoiceDate->addMonthNoOverflow();
        }

        return $periods;
    }
}
