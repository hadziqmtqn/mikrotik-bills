<?php

namespace App\Filament\Resources\InvoiceResource\Actions;

use App\Enums\StatusData;
use App\Helpers\DateHelper;
use App\Models\Application;
use App\Models\BankAccount;
use App\Models\Invoice;
use Filament\Actions\EditAction;
use Illuminate\Contracts\View\View;
use Torgodly\Html2Media\Actions\Html2MediaAction;

class InvoiceActions
{
    public static function actions(): array
    {
        return [
            EditAction::make()
                ->label('Ubah')
                ->icon('heroicon-o-pencil')
                ->color('warning')
                ->visible(fn(Invoice $record): bool => $record->status === StatusData::UNPAID->value || $record->status === StatusData::OVERDUE->value),

            Html2MediaAction::make('export')
                ->label('Cetak')
                ->icon('heroicon-o-printer')
                ->color('primary')
                ->modalHeading('Cetak Invoice')
                ->modalDescription('Apakah Anda yakin ingin mencetak invoice ini?')
                ->successNotificationTitle('Invoice berhasil dicetak.')
                ->savePdf()
                ->content(
                    fn(Invoice $record): View => view('filament.resources.invoice-resource.pages.print', [
                        'invoice' => $record->loadMissing([
                            'user:id,name,email',
                            'user.userProfile',
                            'invCustomerServices.customerService.servicePackage',
                            'invExtraCosts.extraCost:id,name'
                        ]),
                        'application' => Application::first(),
                        'bankAccounts' => BankAccount::where('is_active', true)
                            ->orderBy('bank_name')
                            ->get(),
                    ])
                )
                ->filename(fn(Invoice $record): string => 'invoice-' . $record->code . '-' . DateHelper::indonesiaDate($record->date) . '.pdf')
        ];
    }
}
