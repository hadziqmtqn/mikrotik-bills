<?php

namespace App\Filament\Resources\PaymentResource\Schemas;

use App\Enums\PaymentMethod;
use App\Enums\StatusData;
use App\Helpers\DateHelper;
use App\Models\Payment;
use CodeWithKyrian\FilamentDateRange\Tables\Filters\DateRangeFilter;
use Exception;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PaymentTable
{
    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->description(fn(Payment $record): string => 'Kode Tagihan: ' . $record->invoice?->code)
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('Nama')
                    ->searchable(),

                TextColumn::make('amount')
                    ->label('Jml. Bayar')
                    ->money('idr'),

                TextColumn::make('payment_method')
                    ->label('Metode Bayar')
                    ->formatStateUsing(fn($state): string => PaymentMethod::tryFrom($state)?->getLabel() ?? 'N/A'),

                TextColumn::make('bankAccount.short_name')
                    ->label('Bank Tujuan'),

                TextColumn::make('date')
                    ->label('Tgl. Bayar')
                    ->date()
                    ->formatStateUsing(fn($state): string => DateHelper::indonesiaDate($state, 'D MMM Y')),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn($state): string => StatusData::tryFrom($state)?->getColor() ?? 'gray')
                    ->formatStateUsing(fn($state): string => StatusData::tryFrom($state)?->getLabel() ?? 'N/A'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(StatusData::options(['pending', 'partially_paid', 'paid', 'cancelled']))
                    ->native(false),

                SelectFilter::make('payment_method')
                    ->label('Metode Bayar')
                    ->options(PaymentMethod::options())
                    ->native(false),

                DateRangeFilter::make('date')
                    ->timezone('Asia/Jakarta'),

                TrashedFilter::make()
                    ->native(false),
            ])
            ->filtersFormColumns(2)
            ->actions([
                ViewAction::make()
            ])
            ->bulkActions([
                //
            ]);
    }
}
