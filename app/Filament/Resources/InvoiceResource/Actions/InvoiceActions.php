<?php

namespace App\Filament\Resources\InvoiceResource\Actions;

use App\Enums\StatusData;
use App\Models\Invoice;
use App\Services\CustomerService\CustomerServiceUsageService;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Get;

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
                        ->minDate(function (Invoice $invoice): string {
                            $invoice->loadMissing('invCustomerServices');

                            return CustomerServiceUsageService::lastUsagePeriod($invoice->invCustomerServices->pluck('customer_service_id')->toArray());
                        })
                        ->required()
                        ->placeholder('Masukkan tanggal faktur')
                        ->closeOnDateSelection()
                        ->reactive(),

                    DatePicker::make('due_date')
                        ->label('Tanggal Jatuh Tempo')
                        ->native(false)
                        ->required()
                        ->minDate(fn(Get $get): string => $get('date'))
                        ->default(now()->setDay(20))
                        ->maxDate(now()->endOfMonth())
                        ->placeholder('Masukkan tanggal jatuh tempo')
                        ->closeOnDateSelection(),

                    ToggleButtons::make('status')
                        ->options(StatusData::options(['overdue', 'cancelled']))
                        ->inline()
                ])
                ->mutateFormDataUsing(function (array $data): array {
                    if ($data['status'] == 'cancelled') {
                        $data['cancel_date'] = now();
                    }

                    return $data;
                })
                ->modalWidth('md')
                ->visible(fn(Invoice $record): bool => $record->status === StatusData::UNPAID->value || $record->status === StatusData::OVERDUE->value),

            PrintInvoiceAction::page()
        ];
    }
}
