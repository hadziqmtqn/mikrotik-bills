<?php

namespace App\Filament\Resources\CustomerServiceResource\Pages;

use App\Enums\PackageTypeService;
use App\Enums\PaymentType;
use App\Enums\ServiceType;
use App\Filament\Resources\CustomerServiceResource\CustomerServiceResource;
use App\Models\ExtraCost;
use App\Models\ServicePackage;
use App\Services\CustomerService\CreateCSService;
use App\Services\CustomerService\CreateInvCSService;
use App\Services\CustomerService\CreateInvExtraCostService;
use App\Services\CustomerService\CreateInvoiceService;
use App\Traits\InvoiceSettingTrait;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
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
            $instalationDate = $data['installation_date'] ?? null;
            $servicetype = $servicePackage?->service_type;
            $isHostpot = $servicetype === ServiceType::HOTSPOT->value;
            $date = $isHostpot ? Carbon::now() : ($instalationDate ? Carbon::createFromDate($instalationDate) : null);

            $customerService = CreateCSService::handle(
                userId: $data['user_id'],
                servicePackage: $servicePackage,
                packageType: $isHostpot ? PackageTypeService::ONE_TIME->value : PackageTypeService::SUBSCRIPTION->value,
                installationDate: $date
            );

            // TODO Create Invoice
            $invoice = CreateInvoiceService::handle(
                userId: $customerService->user_id,
                date: $date,
                dueDate: $date->copy()->addDays((int)$this->setting()?->due_date_after_new_service ?? 7)
            );

            // TODO Create Invoice Customer Service Items
            /**
             * Ini berlaku pada saat pertama pasang baru
             * - Dikenakan tagihan di awal jika jenis pembayaran paket adalah PREPAID (PRA BAYAR)
             * - Jika jenis layanan Hostpot, nominal tagihan layanan utama dibabankan
            */

            CreateInvCSService::handle(
                invoiceId: $invoice->id,
                customerService: $customerService,
                includeBill: $servicetype === ServiceType::HOTSPOT->value || $servicePackage?->payment_type === PaymentType::PREPAID->value
            );

            // TODO Create Extra Cost Items
            if (count($data['inv_extra_costs']) > 0) {
                foreach ($data['inv_extra_costs'] as $inv_extra_cost) {
                    $extraCost = ExtraCost::find($inv_extra_cost);

                    CreateInvExtraCostService::handle(
                        invoiceId: $invoice->id,
                        extraCost: $extraCost
                    );
                }
            }

            return $customerService;
        });
    }
}
