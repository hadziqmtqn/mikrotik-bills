<?php

namespace App\Filament\Resources\CustomerServiceResource\Schemas;

use App\Enums\PackageTypeService;
use App\Enums\StatusData;
use App\Helpers\DateHelper;
use Exception;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CustomerServiceTable
{
    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference_number')
                    ->label('No. Referensi')
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('Pelanggan')
                    ->searchable(),

                TextColumn::make('servicePackage.package_name')
                    ->label('Paket Layanan')
                    ->searchable(),

                TextColumn::make('price')
                    ->label('Harga')
                    ->money('idr'),

                TextColumn::make('package_type')
                    ->label('Jenis Paket')
                    ->searchable()
                    ->formatStateUsing(fn($state): string => PackageTypeService::tryFrom($state)?->getLabel() ?? 'N/A'),

                TextColumn::make('start_date')
                    ->label('Tanggal Mulai')
                    ->date()
                    ->formatStateUsing(fn($state): string => $state ? DateHelper::indonesiaDate($state, 'D MMM Y HH:mm') : null)
                    ->toggleable()
                    ->toggledHiddenByDefault(),

                TextColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->formatStateUsing(fn($state): string => StatusData::tryFrom($state)?->getLabel() ?? 'N/A')
                    ->color(fn($state): string => StatusData::tryFrom($state)?->getColor() ?? 'gray'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('package_type')
                    ->label('Jenis Paket')
                    ->options(PackageTypeService::options())
                    ->native(false),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options(StatusData::options(['pending', 'active', 'suspended', 'cancelled']))
                    ->native(false),

                TrashedFilter::make()
                    ->label('Termasuk yang Dihapus')
                    ->native(false),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                    RestoreAction::make(),
                    ForceDeleteAction::make(),
                ])
                ->link()
                ->label('Actions')
            ])
            ->bulkActions([
                //
            ]);
    }
}
