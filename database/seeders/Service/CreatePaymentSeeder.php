<?php

namespace Database\Seeders\Service;

use App\Enums\PackageTypeService;
use App\Enums\PaymentMethod;
use App\Enums\StatusData;
use App\Models\CustomerService;
use App\Models\ExtraCost;
use App\Models\Payment;
use App\Services\CustomerService\CreateInvCSService;
use App\Services\CustomerService\CreateInvExtraCostService;
use App\Services\CustomerService\CreateInvoiceService;
use App\Services\CustomerService\CustomerServiceUsageService;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class CreatePaymentSeeder extends Seeder
{
    /**
     * @throws Exception
     */
    public function run(): void
    {
        $customerServices = CustomerService::query()
            ->where([
                'package_type' => PackageTypeService::SUBSCRIPTION->value,
                'status' => StatusData::ACTIVE->value
            ])
            ->get();

        foreach ($customerServices as $customerService) {
            $this->firstStep($customerService);
        }
    }

    private function extraCosts($billingType = null): Collection
    {
        return ExtraCost::when($billingType, fn(Builder $query) => $query->where('billing_type', $billingType))
            ->get();
    }

    /**
     * @throws Exception
     */
    private function firstStep(CustomerService $customerService): void
    {
        $extraCosts = $this->extraCosts();

        $userId = $customerService->user_id;
        $date = Carbon::parse($customerService->installation_date);

        // 1. create invoice
        $invoice = CreateInvoiceService::handle(
            userId: $userId,
            date: $date->copy(),
            dueDate: $date->copy()->addDays(7),
            defaultStatus: StatusData::PAID->value
        );

        // 2. create invoice customer service
        CreateInvCSService::handle(
            invoiceId: $invoice->id,
            customerService: $customerService,
            includeBill: false
        );

        // 3. create extra costs
        $totalFee = 0;
        foreach ($extraCosts as $extraCost) {
            CreateInvExtraCostService::handle(
                invoiceId: $invoice->id,
                extraCost: $extraCost
            );

            $totalFee += $extraCost->fee;
        }

        // 4. create payment
        $payment = new Payment();
        $payment->user_id = $userId;
        $payment->invoice_id = $invoice->id;
        $payment->payment_method = PaymentMethod::CASH->value;
        $payment->date = $date->copy();
        $payment->amount = $totalFee;
        $payment->status = StatusData::PAID->value;
        $payment->save();

        // 5. create customer service usage
        CustomerServiceUsageService::handle(
            customerService: $customerService,
            invoiceId: $invoice->id
        );
    }
}
