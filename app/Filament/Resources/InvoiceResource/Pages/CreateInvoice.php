<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Enums\StatusData;
use App\Filament\Resources\InvoiceResource\InvoiceResource;
use App\Models\CustomerService;
use App\Models\InvCustomerService;
use App\Services\CustomerService\CreateInvCSService;
use App\Services\CustomerService\CreateInvoiceService;
use App\Services\CustomerService\CSService;
use App\Services\CustomerService\RecalculateInvoiceTotalService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Throwable;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected static ?string $title = 'Tambah Faktur';

    protected static bool $canCreateAnother = false;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

    /**
     * @throws Halt
     */
    protected function beforeValidate(): void
    {
        // Runs before the form fields are saved to the database.
        $data = $this->form->getState();
        $customerServices = $data['customer_services'] ?? [];

        $serviceItems = [];
        foreach ($customerServices as $customerService) {
            $serviceItems[] = $customerService['customer_service_id'];
        }

        $unpaidInvoices = InvCustomerService::query()
            ->whereHas('invoice', function (Builder $query) use ($data) {
                $query->where('user_id', $data['user_id']);
                $query->where('status', StatusData::UNPAID->value);
            })
            ->whereIn('id', $serviceItems)
            ->exists();

        if ($unpaidInvoices) {
            Notification::make()
                ->warning()
                ->title('Harap lunasi tagihan layanan yang belum lunas')
                ->send();

            throw new Halt();
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $collection = collect($data['customer_services']);

        $customerServiceIds = $collection
            ->pluck('customer_service_id')
            ->all();

        $customerServices = CSService::options(userId: $data['user_id'], selfIds: $customerServiceIds);

        $data['customer_services'] = $collection
            ->map(function ($item) use ($customerServices) {
                $csId = (int) $item['customer_service_id'];
                $cs = $customerServices[$csId] ?? null;

                return [
                    'customer_service_id' => $csId,
                    'include_bill' => $cs['includeBill'] ?? false,
                    'extra_costs' => $cs['additionalServiceFees'] ?? [],
                ];
            })
            ->values()
            ->toArray();

        return $data;
    }

    /**
     * @throws Throwable
     */
    protected function handleRecordCreation(array $data): Model
    {
        /**
         * Cek tagihan sesuai layanan pelanggan pada bulan saat ini dengan status sudah lunas atau masih tertunda
         * jika ada tolak pembuatan faktur baru agar tidak duplikat
        */

        return DB::transaction(function () use ($data) {
            // Create invoice
            $invoice = CreateInvoiceService::handle(
                userId: $data['user_id'],
                date: $data['date'],
                dueDate: $data['due_date'],
                defaultNote: 'Dibuat manual oleh admin'
            );

            // Create customer services
            foreach ($data['customer_services'] as $cs) {
                $customerService = CustomerService::find($cs['customer_service_id']);

                if (!$customerService) {
                    Notification::make()
                        ->danger()
                        ->title('Layanan pelanggan tidak ditemukan')
                        ->send();

                    $this->halt();
                }

                $extraCosts = collect($cs['extra_costs']);

                CreateInvCSService::handle(
                    invoiceId: $invoice->id,
                    customerService: $customerService,
                    includeBill: $cs['include_bill'],
                    extraCosts: count($extraCosts) > 0 ? $extraCosts->toArray() : null
                );
            }

            return $invoice;
        });
    }

    protected function afterCreate(): void
    {
        RecalculateInvoiceTotalService::totalPrice($this->record);
    }
}
