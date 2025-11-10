<?php

namespace App\Filament\Clusters\Reference\Resources\ExtraCostResource\Tables;

use App\Enums\BillingType;
use App\Enums\StatusData;
use Filament\Actions\DeleteAction;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExtraCostTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),

                TextColumn::make('fee')
                    ->label('Biaya')
                    ->money('IDR')
                    ->searchable(),

                TextColumn::make('billing_type')
                    ->label('Jenis Tagihan')
                    ->formatStateUsing(fn($state): string => BillingType::tryFrom($state)?->getLabel() ?? $state),

                TextColumn::make('is_active')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state): string => StatusData::tryFrom(($state ? 'active' : 'inactive'))?->getColor() ?? 'gray')
                    ->formatStateUsing(fn($state): string => StatusData::tryFrom(($state ? 'active' : 'inactive'))?->getLabel() ?? $state)
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make()
                        ->modalHeading('Ubah Biaya Tambahan')
                        ->modalWidth(MaxWidth::Medium),
                    DeleteAction::make()
                        ->modalHeading('Hapus Biaya Tambahan')
                ])
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
