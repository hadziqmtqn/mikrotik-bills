<?php

namespace App\Filament\Resources\PaymentResource\Schemas;

use App\Enums\PaymentMethod;
use App\Enums\StatusData;
use App\Helpers\DateHelper;
use Exception;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
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
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('Nama')
                    ->searchable(),

                TextColumn::make('invoice.code')
                    ->label('Kode Faktur')
                    ->searchable(),

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
                TrashedFilter::make(),
            ])
            ->actions([
                ViewAction::make()
            ])
            ->bulkActions([
                //
            ]);
    }
}
