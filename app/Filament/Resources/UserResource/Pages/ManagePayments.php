<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Enums\PaymentMethod;
use App\Enums\StatusData;
use App\Filament\Resources\PaymentResource\Pages\ViewPayment;
use App\Filament\Resources\UserResource;
use App\Helpers\DateHelper;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ManagePayments extends ManageRelatedRecords
{
    protected static string $resource = UserResource::class;

    protected static string $relationship = 'payments';

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $title = 'Pembayaran';

    protected static ?string $navigationLabel = 'Pembayaran';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable(),

                TextColumn::make('date')
                    ->label('Tanggal')
                    ->formatStateUsing(fn($state): string => DateHelper::indonesiaDate($state)),

                TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR'),

                TextColumn::make('payment_method')
                    ->label('Metode Bayar')
                    ->formatStateUsing(fn($state): string => PaymentMethod::tryFrom($state)?->getLabel() ?? $state),

                TextColumn::make('bankAccount.sort_name')
                    ->label('Bank Tujuan')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state): string => StatusData::tryFrom($state)?->getColor() ?? 'gray')
                    ->formatStateUsing(fn($state): string => StatusData::tryFrom($state)?->getLabel() ?? $state),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make()
                    ->modalHeading('Detail Pembayaran')
                    ->slideOver()
            ]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return ViewPayment::infolist($infolist);
    }
}
