<?php

namespace App\Filament\Resources\CustomerServiceResource\Pages;

use App\Filament\Resources\CustomerServiceResource\CustomerServiceResource;
use App\Models\CustomerService;
use App\Models\ExtraCost;
use App\Models\InvCustomerService;
use App\Models\InvExtraCost;
use App\Models\Invoice;
use App\Models\ServicePackage;
use App\Traits\InvoiceSettingTrait;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Throwable;

class CreateCustomerService extends CreateRecord
{
    use InvoiceSettingTrait;

    protected static string $resource = CustomerServiceResource::class;

    protected static ?string $title = 'Buat Layanan Pelanggan';

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

    /**
     * @throws Throwable
     */
    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $servicePackage = ServicePackage::find($data['service_package_id']);

            $customerService = new CustomerService();
            $customerService->user_id = $data['user_id'];
            $customerService->service_package_id = $servicePackage?->id;
            $customerService->price = $servicePackage?->package_price;
            $customerService->package_type = $data['package_type'];
            $customerService->save();

            // TODO Create Invoice
            $invoice = new Invoice();
            $invoice->user_id = $customerService->user_id;
            $invoice->date = now();
            $invoice->due_date = now()->addDays($this->setting()?->due_date_after_new_service);
            $invoice->note = 'Dibuat secara otomatis oleh sistem';
            $invoice->save();

            // TODO Create Invoice Customer Service Items
            $invoiceItem = new InvCustomerService();
            $invoiceItem->invoice_id = $invoice->id;
            $invoiceItem->customer_service_id = $customerService->id;
            $invoiceItem->amount = $customerService->price;
            $invoiceItem->save();

            // TODO Create Extra Cost Items
            if (count($data['inv_extra_costs']) > 0) {
                foreach ($data['inv_extra_costs'] as $inv_extra_cost) {
                    $extraCost = ExtraCost::find($inv_extra_cost);

                    $invExtraCost = new InvExtraCost();
                    $invExtraCost->invoice_id = $invoice->id;
                    $invExtraCost->extra_cost_id = $extraCost?->id;
                    $invExtraCost->fee = $extraCost?->fee;
                    $invExtraCost->save();
                }
            }

            return $customerService;
        });
    }
}
