<?php

namespace App\Filament\Resources\InvoiceResource\Actions;

use App\Enums\StatusData;
use App\Models\Invoice;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;

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

            PrintInvoiceAction::page()
        ];
    }
}
