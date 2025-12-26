<?php

namespace App\Filament\Resources\CustomerServiceResource\Tables;

use App\Enums\PackageTypeService;
use App\Enums\PaymentType;
use App\Enums\ServiceType;
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
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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

                TextColumn::make('servicePackage.service_type')
                    ->label('Jenis Layanan')
                    ->badge()
                    ->color(fn($state): string => ServiceType::tryFrom($state)?->getColor() ?? 'gray')
                    ->formatStateUsing(fn($state): string => ServiceType::tryFrom($state)?->getLabel() ?? $state)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('servicePackage.package_name')
                    ->label('Paket Layanan')
                    ->searchable(),

                TextColumn::make('servicePackage.payment_type')
                    ->label('Jenis Pembayaran')
                    ->formatStateUsing(fn($state): string => PaymentType::tryFrom($state)?->getLabel() ?? $state)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('price')
                    ->label('Harga')
                    ->money('idr'),

                TextColumn::make('package_type')
                    ->label('Jenis Paket')
                    ->searchable()
                    ->formatStateUsing(fn($state): string => PackageTypeService::tryFrom($state)?->getLabel() ?? $state),

                TextColumn::make('installation_date')
                    ->label('Tanggal Instalasi')
                    ->date()
                    ->formatStateUsing(fn($state): string => $state ? DateHelper::indonesiaDate($state, 'D MMM Y') : null)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('start_date')
                    ->label('Tanggal Aktivasi')
                    ->date()
                    ->formatStateUsing(fn($state): string => $state ? DateHelper::indonesiaDate($state, 'D MMM Y') : null)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('customerServiceUsageLatest.next_billing_date')
                    ->label('Tgl. Tagihan Berikutnya')
                    ->date()
                    ->formatStateUsing(fn($state): string => $state ? DateHelper::indonesiaDate($state, 'D MMM Y') : null)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->formatStateUsing(fn($state): string => StatusData::tryFrom($state)?->getLabel() ?? 'N/A')
                    ->color(fn($state): string => StatusData::tryFrom($state)?->getColor() ?? 'gray'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filtersFormColumns(4)
            ->deferLoading()
            ->filters([
                SelectFilter::make('service_type')
                    ->label('Jenis Layanan')
                    ->options(ServiceType::options())
                    ->native(false)
                    ->query(function (Builder $query, array $data): Builder {
                        $type = $data['value'] ?? null;

                        return $query->when($type, function (Builder $query) use ($type) {
                            $query->whereHas('servicePackage', fn(Builder $query) => $query->where('service_type', $type));
                        });
                    }),

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
                    ->native(false)
                    ->placeholder('Pilih'),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    DeleteAction::make()->modalHeading('Hapus Layanan Pelanggan'),
                    RestoreAction::make()->modalHeading('Pulihkan Data'),
                    ForceDeleteAction::make()->modalHeading('Hapus Selamanya'),
                ])
            ])
            ->bulkActions([
                //
            ]);
    }
}
