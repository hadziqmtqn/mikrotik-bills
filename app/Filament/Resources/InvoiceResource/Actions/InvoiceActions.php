<?php

namespace App\Filament\Resources\InvoiceResource\Actions;

use App\Enums\StatusData;
use App\Helpers\DateHelper;
use App\Models\Application;
use App\Models\BankAccount;
use App\Models\Invoice;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Illuminate\Contracts\View\View;
use Torgodly\Html2Media\Actions\Html2MediaAction;

class InvoiceActions
{
    public static function actions(): array
    {
        return [
            EditAction::make()
                ->label('Ubah')
                ->modalHeading('Ubah Faktur')
                ->icon('heroicon-o-pencil')
                ->color('warning')
                ->form([
                    DatePicker::make('date')
                        ->label('Tanggal')
                        ->native(false)
                        ->default(now())
                        ->required()
                        ->placeholder('Masukkan tanggal faktur')
                        ->closeOnDateSelection(),

                    DatePicker::make('due_date')
                        ->label('Tanggal Jatuh Tempo')
                        ->native(false)
                        ->required()
                        ->default(now()->setDay(20))
                        ->maxDate(now()->endOfMonth())
                        ->placeholder('Masukkan tanggal jatuh tempo')
                        ->closeOnDateSelection(),
                ])
                ->modalWidth('md')
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
                            'invExtraCosts.extraCost:id,name',
                            'payments'
                        ]),
                        'application' => Application::first(),
                        'bankAccounts' => BankAccount::query()
                            ->where('is_active', true)
                            ->orderBy('bank_name')
                            ->get(),
                    ])
                )
                ->filename(fn(Invoice $record): string => 'invoice-' . $record->code . '-' . DateHelper::indonesiaDate($record->date) . '.pdf')
        ];
    }
}
