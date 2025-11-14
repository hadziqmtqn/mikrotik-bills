<?php

namespace App\Filament\Resources\PaymentResource\Schemas;

use App\Enums\PaymentMethod;
use App\Enums\StatusData;
use App\Helpers\DateHelper;
use App\Models\Payment;
use CodeWithKyrian\FilamentDateRange\Tables\Filters\DateRangeFilter;
use Exception;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Query\Builder;

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
                    ->money('idr')
                    ->summarize([
                        Sum::make('amount')
                            ->money('idr')
                            ->query(fn(Builder $query) => $query->where('status', 'paid'))
                    ]),

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
                SelectFilter::make('payment_method')
                    ->label('Metode Bayar')
                    ->options(PaymentMethod::options())
                    ->native(false),

                DateRangeFilter::make('date')
                    ->timezone('Asia/Jakarta'),

                TrashedFilter::make()
                    ->native(false),
            ], layout: FiltersLayout::Modal)
            ->filtersFormWidth(MaxWidth::Large)
            ->actions([
                ViewAction::make()
                    ->modalHeading('Detail Pembayaran')
                    ->button()
            ])
            ->bulkActions([
                //
            ]);
    }
}
