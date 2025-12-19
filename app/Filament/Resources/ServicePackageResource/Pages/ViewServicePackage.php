<?php

namespace App\Filament\Resources\ServicePackageResource\Pages;

use App\Enums\PackageLimitType;
use App\Enums\PaymentType;
use App\Enums\ServiceType;
use App\Filament\Resources\ServicePackageResource;
use App\Models\ServicePackage;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewServicePackage extends ViewRecord
{
    protected static string $resource = ServicePackageResource::class;

    protected static ?string $title = 'Detail Paket Layanan';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->columns(3)
            ->components([
                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Section::make('Layanan')
                            ->columns(['lg' => 2, 'sm' => 2])
                            ->collapsible()
                            ->schema([
                                TextEntry::make('code')
                                    ->label('Kode'),

                                TextEntry::make('service_type')
                                    ->label('Jenis Layanan')
                                    ->badge()
                                    ->color(fn($state): string => ServiceType::tryFrom($state)?->getColor() ?? 'gray')
                                    ->formatStateUsing(fn($state): string => ServiceType::tryFrom($state)?->getLabel() ?? $state),

                                TextEntry::make('payment_type')
                                    ->label('Jenis Pembayaran')
                                    ->badge()
                                    ->color(fn($state): string => PaymentType::tryFrom($state)?->getColor() ?? 'gray')
                                    ->formatStateUsing(fn($state): string => PaymentType::tryFrom($state)?->getLabel() ?? $state),

                                TextEntry::make('package_name')
                                    ->label('Paket Layanan'),

                                TextEntry::make('plan_type')
                                    ->label('Jenis Paket')
                                    ->formatStateUsing(fn($state): string => ucfirst($state)),

                                TextEntry::make('router.name')
                                    ->label('Ruter')
                            ]),

                        Section::make('Pengaturan Layanan')
                            ->inlineLabel()
                            ->schema([
                                TextEntry::make('package_limit_type')
                                    ->label('Jenis Batasan Paket')
                                    ->formatStateUsing(fn($state): string => PackageLimitType::tryFrom($state)?->getLabel() ?? $state)
                                    ->visible(fn(ServicePackage $record): bool => $record->service_type == ServiceType::HOTSPOT->value),


                            ])
                    ]),

                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        Section::make()
                    ]),
            ]);
    }
}
