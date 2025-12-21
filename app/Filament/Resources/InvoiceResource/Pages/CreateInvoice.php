<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Enums\StatusData;
use App\Filament\Resources\InvoiceResource\InvoiceResource;
use App\Models\CustomerService;
use App\Models\ExtraCost;
use App\Models\InvCustomerService;
use App\Services\CustomerService\CreateInvCSService;
use App\Services\CustomerService\CreateInvExtraCostService;
use App\Services\CustomerService\CreateInvoiceService;
use App\Services\RecalculateInvoiceTotalService;
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

        $invoiceThisMonth = InvCustomerService::query()
            ->whereHas('invoice', function (Builder $query) use ($data) {
                $query->where('user_id', $data['user_id']);
                $query->whereIn('status', [StatusData::PAID->value, StatusData::UNPAID->value]);
                $query->whereMonth('date', date('m', strtotime($data['date'])));
                $query->whereYear('date', date('Y', strtotime($data['date'])));
            })
            ->whereIn('id', $data['inv_customer_services'])
            ->exists();

        if ($invoiceThisMonth) {
            Notification::make()
                ->warning()
                ->title('Faktur bulan ini telah dibuat dengan status lunas atau belum dibayar')
                ->send();

            throw new Halt();
        }
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
            $invoice = CreateInvoiceService::handle(
                userId: $data['userid'],
                date: $data['date'],
                dueDate: $data['due_date'],
                defaultNote: 'Dibuat manual oleh admin'
            );

            // Customer Serives
            foreach ($data['invCustomerServices'] as $inv_customer_service) {
                $customerService = CustomerService::find($inv_customer_service);

                CreateInvCSService::handle(
                    invoiceId: $invoice->id,
                    customerService: $customerService,
                    includeBill: true
                );
            }

            // Extra Cost
            foreach ($data['invExtraCosts'] as $extra_cost) {
                $extraCost = ExtraCost::find($extra_cost);

                CreateInvExtraCostService::handle(
                    invoiceId: $invoice,
                    extraCost: $extraCost
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
