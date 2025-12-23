<?php

namespace App\Observers;

use App\Enums\StatusData;
use App\Models\CustomerService;
use App\Models\Invoice;
use App\Services\CustomerService\CustomerServiceUsageService;
use App\Traits\InvoiceSettingTrait;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class InvoiceObserver
{
    use InvoiceSettingTrait;

    public function creating(Invoice $invoice): void
    {
        $invoice->slug = Str::uuid()->toString();
        $invoice->serial_number = Invoice::max('serial_number') + 1;
        $invoice->code = 'INV' . Str::padLeft($invoice->serial_number, 6, '0');
    }

    /**
     * @throws Exception
     */
    public function updated(Invoice $invoice): void
    {
        $invoice->refresh();

        // Perbarui tanggal dibatalkan jika faktur jatur tempo
        if ($invoice->status === StatusData::OVERDUE->value) {
            /**
             * Tagihan yang sudah jatuh tempo dan belum dibayar sampai 7 hari kedepan, akan otomatis dibatalkan
            */
            $invoice->update([
                'cancel_date' => Carbon::parse($invoice->due_date)->addDays($this->setting()?->cancel_after ?? 7)
            ]);
        }

        // Tulis catatan pada faktur jika telah dibatalkan otomatis
        if ($invoice->status === StatusData::CANCELLED->value) {
            CustomerService::whereHas('invCustomerServices', fn($query) => $query->where('invoice_id', $invoice->id))
                ->update([
                    'status' => StatusData::CANCELLED->value,
                    'notes' => 'Layanan pelanggan dibatalkan otomatis karena tagihan tidak dibayar setelah tanggal jatuh tempo.',
                ]);
        }

        // Jika faktur lunas, aktifkan semua layanan pelanggan
        if ($invoice->status === StatusData::PAID->value) {
            $invCustomerServices = $invoice->invCustomerServices;
            foreach ($invCustomerServices as $item) {
                $customerService = $item->customerService;

                $customerService->status = StatusData::ACTIVE->value;
                $customerService->save();

                // TODO Catat penggunaan layanan
                CustomerServiceUsageService::handle(customerService: $customerService, invoiceId: $invoice->id);
            }
        }
    }
}
