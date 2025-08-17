<?php

namespace App\Filament\Resources\CustomerServiceResource\Pages;

use App\Enums\StatusData;
use App\Filament\Resources\CustomerServiceResource;
use App\Helpers\DateHelper;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InvoiceHistory extends ManageRelatedRecords
{
    protected static string $resource = CustomerServiceResource::class;
    protected static string $relationship = 'invoiceItems';
    protected static ?string $title = 'Invoice History';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice.code')
                    ->label('Kode')
                    ->searchable(),

                TextColumn::make('invoice.user.name')
                    ->label('Pelanggan')
                    ->searchable(),

                TextColumn::make('invoice.date')
                    ->label('Tanggal')
                    ->date()
                    ->formatStateUsing(fn ($state): string => DateHelper::indonesiaDate($state, 'D MMM Y HH:mm')),

                TextColumn::make('invoice.due_date')
                    ->label('Jatuh Tempo')
                    ->date()
                    ->formatStateUsing(fn ($state): string => DateHelper::indonesiaDate($state, 'D MMM Y HH:mm')),

                TextColumn::make('invoice.total_price')
                    ->label('Total Harga')
                    ->money('idr'),

                TextColumn::make('invoice.status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn($state): string => StatusData::tryFrom($state)->getLabel())
                    ->color(fn($state): string => StatusData::tryFrom($state)->getColor()),

                TextColumn::make('invoice.cancel_date')
                    ->label('Tanggal Dibatalkan')
                    ->date()
                    ->formatStateUsing(fn ($state): string => DateHelper::indonesiaDate($state, 'D MMM Y HH:mm')),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ]);
    }
}
