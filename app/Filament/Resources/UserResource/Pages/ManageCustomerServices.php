<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Enums\PackageTypeService;
use App\Enums\StatusData;
use App\Filament\Resources\CustomerServiceResource\Pages\ViewCustomerService;
use App\Filament\Resources\UserResource\UserResource;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ManageCustomerServices extends ManageRelatedRecords
{
    protected static string $resource = UserResource::class;

    protected static string $relationship = 'customerServices';

    protected static ?string $title = 'Layanan';

    protected static ?string $navigationIcon = 'heroicon-o-server-stack';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('servicePackage.package_name')
                    ->label('Nama Paket')
                    ->searchable(),

                TextColumn::make('package_type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn($state): string => PackageTypeService::tryFrom($state)?->getColor() ?? 'gray')
                    ->formatStateUsing(fn($state): string => PackageTypeService::tryFrom($state)?->getLabel() ?? $state)
                    ->searchable(),

                TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state): string => StatusData::tryFrom($state)?->getColor() ?? 'gray')
                    ->formatStateUsing(fn($state): string => StatusData::tryFrom($state)?->getLabel() ?? $state)
            ])
            ->actions([
                ViewAction::make()
                    ->modalHeading('Detail Layanan Pelanggan')
                    ->button()
                    ->outlined()
                    ->slideOver()
            ]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return ViewCustomerService::columns($infolist);
    }
}
