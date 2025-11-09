<?php

namespace App\Filament\Resources\ServicePackageResource\Schemas;

use App\Enums\AccountType;
use App\Enums\ServiceType;
use App\Models\ServicePackage;
use Exception;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ServicePackageTable
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

                TextColumn::make('service_type')
                    ->label('Tipe Layanan')
                    ->badge()
                    ->color(fn($state): string => ServiceType::tryFrom($state)?->getColor() ?? 'secondary')
                    ->formatStateUsing(fn($state): string => ServiceType::tryFrom($state)?->getLabel() ?? 'N/A')
                    ->sortable(),

                TextColumn::make('package_name')
                    ->label('Nama Paket Layanan')
                    ->searchable(),

                TextColumn::make('plan_type')
                    ->label('Tipe Paket')
                    ->badge()
                    ->icon(fn($state): string => AccountType::tryFrom($state)?->getIcon() ?? 'heroicon-o-question-mark-circle')
                    ->color(fn($state): string => AccountType::tryFrom($state)?->getColor() ?? 'secondary')
                    ->formatStateUsing(fn($state): string => AccountType::tryFrom($state)?->getLabel() ?? 'N/A')
                    ->sortable(),

                TextColumn::make('package_price')
                    ->label('Harga Paket')
                    ->money('idr')
                    ->sortable(),

                TextColumn::make('router.name')
                    ->label('Router')
                    ->getStateUsing(fn(ServicePackage $record) => $record->router?->name ?? '-')
                    ->sortable(),

                ToggleColumn::make('is_active')
                    ->label('Aktif')
                    ->sortable()
                    ->onIcon('heroicon-o-check-circle')
                    ->offIcon('heroicon-o-x-circle'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('service_type')
                    ->label('Tipe Layanan')
                    ->options([
                        'hotspot' => 'Hotspot',
                        'pppoe' => 'PPPoE',
                    ])
                    ->native(false)
                    ->placeholder('Semua Tipe Layanan')
                    ->searchable(),
                TrashedFilter::make()
                    ->native(false),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()->modalHeading('Hapus paket layanan'),
                    RestoreAction::make()->modalHeading('Pulihkan data'),
                    ForceDeleteAction::make()->modalHeading('Hapus selamanya'),
                ])
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
