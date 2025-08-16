<?php

namespace App\Filament\Resources\InvoiceResource\Schemas;

use App\Enums\StatusData;
use App\Helpers\DateHelper;
use Exception;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InvoiceTable
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
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Pelanggan')
                    ->searchable(),

                TextColumn::make('total_price')
                    ->label('Total Harga')
                    ->money('idr'),

                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date()
                    ->formatStateUsing(fn($state): string => DateHelper::indonesiaDate($state, 'D MMM Y HH:mm')),

                TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date()
                    ->formatStateUsing(fn($state): string => DateHelper::indonesiaDate($state, 'D MMM Y HH:mm')),

                TextColumn::make('cancel_date')
                    ->label('Tanggal Batal')
                    ->date()
                    ->formatStateUsing(fn($state): string => DateHelper::indonesiaDate($state, 'D MMM Y HH:mm'))
                    ->toggleable()
                    ->toggledHiddenByDefault(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn($state): string => StatusData::tryFrom($state)?->getLabel() ?? 'Unknown')
                    ->color(fn($state): string => StatusData::tryFrom($state)?->getColor() ?? 'gray'),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(StatusData::options(['unpaid', 'paid', 'overdue', 'cancelled']))
                    ->native(false)
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                //
            ]);
    }
}
