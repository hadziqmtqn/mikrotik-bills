<?php

namespace Database\Seeders\Service;

use App\Enums\PackageTypeService;
use App\Enums\PaymentMethod;
use App\Enums\PaymentType;
use App\Enums\ServiceType;
use App\Enums\StatusData;
use App\Models\CustomerService;
use App\Models\ExtraCost;
use App\Models\Payment;
use App\Services\CustomerService\AdditionalServiceFeeService;
use App\Services\CustomerService\CreateInvCSService;
use App\Services\CustomerService\CreateInvoiceService;
use App\Services\CustomerService\CustomerServiceUsageService;
use App\Services\RecalculateInvoiceTotalService;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

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

    /**
     * @throws Exception
     */
    private function firstStep(CustomerService $customerService): void
    {
        $customerService->loadMissing('servicePackage');

        $userId = $customerService->user_id;
        $date = Carbon::parse($customerService->installation_date);
        $servicePackage = $customerService->servicePackage;

        // 1. create invoice
        $invoice = CreateInvoiceService::handle(
            userId: $userId,
            date: $date->copy(),
            dueDate: $date->copy()->addDays(7),
            defaultStatus: StatusData::PAID->value
        );

        // 2. create invoice customer service
        $invCustomerService = CreateInvCSService::handle(
            invoiceId: $invoice->id,
            customerService: $customerService,
            includeBill: $servicePackage?->service_type === ServiceType::HOTSPOT->value || $servicePackage?->payment_type === PaymentType::PREPAID->value
        );

        $invCustomerService->refresh();
        // 3. Create Additional seevice fee
        AdditionalServiceFeeService::handleBulk(
            customerServiceId: $customerService->id,
            extraCosts: ExtraCost::all(),
            invCustomerService: $invCustomerService
        );

        // 4. refresh and recalculate invoice total price
        RecalculateInvoiceTotalService::totalPrice($invoice);
        $invoice->refresh();

        // 5. create payment
        $payment = new Payment();
        $payment->user_id = $userId;
        $payment->invoice_id = $invoice->id;
        $payment->payment_method = PaymentMethod::CASH->value;
        $payment->date = $date->copy();
        $payment->amount = $invoice->total_price;
        $payment->status = StatusData::PAID->value;
        $payment->save();

        // 5. create customer service usage
        CustomerServiceUsageService::handle(
            customerService: $customerService,
            invoice: $invoice
        );
    }
}
