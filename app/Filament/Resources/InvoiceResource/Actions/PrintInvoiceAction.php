<?php

namespace App\Filament\Resources\InvoiceResource\Actions;

use App\Models\Application;
use App\Models\BankAccount;
use App\Models\Invoice;
use App\Models\InvCustomerService;
use App\Helpers\DateHelper;
use Illuminate\Contracts\View\View;
use Torgodly\Html2Media\Actions\Html2MediaAction as PageAction;
use Torgodly\Html2Media\Tables\Actions\Html2MediaAction as TableAction;

class PrintInvoiceAction
{
    public static function page(): PageAction
    {
        return self::base(PageAction::class);
    }

    public static function table(): TableAction
    {
        return self::base(TableAction::class);
    }

    protected static function base(string $actionClass)
    {
        return $actionClass::make('export')
            ->label('Cetak')
            ->icon('heroicon-o-printer')
            ->color('primary')
            ->modalHeading('Cetak Faktur')
            ->modalDescription('Apakah Anda yakin ingin mencetak faktur ini?')
            ->requiresConfirmation()
            ->successNotificationTitle('Invoice berhasil dicetak.')
            ->savePdf()
            ->content(fn ($record): View => self::view($record))
            ->filename(fn ($record): string => self::filename($record));
    }

    protected static function view($record): View
    {
        if ($record instanceof InvCustomerService) {
            $record->loadMissing([
                'invoice.user:id,name,email',
                'invoice.user.userProfile',
                'invoice.invCustomerServices.customerService.servicePackage',
            ]);

            $invoice = $record->invoice;
        } else {
            /** @var Invoice $record */
            $record->loadMissing([
                'user:id,name,email',
                'user.userProfile',
                'invCustomerServices.customerService.servicePackage',
                'payments',
            ]);

            $invoice = $record;
        }

        return view('filament.resources.invoice-resource.pages.print', [
            'invoice' => $invoice,
            'application' => Application::first(),
            'bankAccounts' => BankAccount::query()
                ->where('is_active', true)
                ->orderBy('bank_name')
                ->get(),
        ]);
    }

    protected static function filename($record): string
    {
        $invoice = $record instanceof InvCustomerService
            ? $record->invoice
            : $record;

        return 'invoice-' .
            $invoice?->code .
            '-' .
            DateHelper::indonesiaDate($invoice?->date) .
            '.pdf';
    }
}
