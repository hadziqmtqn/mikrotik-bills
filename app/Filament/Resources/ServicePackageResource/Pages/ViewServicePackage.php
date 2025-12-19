<?php

namespace App\Filament\Resources\ServicePackageResource\Pages;

use App\Enums\LimitType;
use App\Enums\PackageLimitType;
use App\Enums\PaymentType;
use App\Enums\ServiceType;
use App\Enums\StatusData;
use App\Enums\TimeLimitType;
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
        $servicePackage = ServicePackage::findOrFail($this->record->id);

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

                        Section::make('Batasan Layanan')
                            ->inlineLabel()
                            ->collapsible()
                            ->schema([
                                TextEntry::make('package_limit_type')
                                    ->label('Jenis Batasan Paket')
                                    ->formatStateUsing(fn($state): string => PackageLimitType::tryFrom($state)?->getLabel() ?? $state)
                                    ->visible(fn(): bool => $servicePackage->service_type == ServiceType::HOTSPOT->value),

                                Group::make()
                                    ->visible(fn(): bool => $servicePackage->package_limit_type === PackageLimitType::LIMITED->value)
                                    ->schema([
                                        TextEntry::make('limit_type')
                                            ->label('Jenis Batasan')
                                            ->formatStateUsing(fn($state): string => LimitType::tryFrom($state)?->getLabel() ?? $state),

                                        TextEntry::make('time_limit')
                                            ->label('Batasan Waktu')
                                            ->visible(fn(): bool => $servicePackage->limit_type != LimitType::DATA->value)
                                            ->formatStateUsing(fn($state): string => $state . ' ' . TimeLimitType::tryFrom($servicePackage->time_limit_unit)?->getLabel() ?? $state),

                                        TextEntry::make('data_limit')
                                            ->label('Batasan Data')
                                            ->visible(fn(): bool => $servicePackage->limit_type != LimitType::TIME->value)
                                            ->formatStateUsing(fn($state): string => $state . ' ' . $servicePackage->data_limit_unit),
                                    ]),

                                Group::make()
                                    ->visible(fn(): bool => $servicePackage->service_type == ServiceType::PPPOE->value)
                                    ->schema([
                                        TextEntry::make('validity_period')
                                            ->label('Masa Berlaku')
                                            ->formatStateUsing(fn($state): string => $state . ' ' . TimeLimitType::tryFrom($servicePackage->validity_unit)?->getLabel() ?? 'N/A')
                                    ])
                            ]),

                        Section::make('Harga Paket')
                            ->collapsible()
                            ->columns()
                            ->schema([
                                TextEntry::make('package_price')
                                    ->label('Harga Paket')
                                    ->money('IDR'),

                                TextEntry::make('price_before_discount')
                                    ->label('Harga Sebelum Diskon')
                                    ->money('IDR'),
                            ])
                    ]),

                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        Section::make('Lainnya')
                            ->collapsible()
                            ->schema([
                                TextEntry::make('description')
                                    ->label('Deskripsi'),

                                TextEntry::make('is_active')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn($state): string => StatusData::tryFrom(($state ? 'active' : 'inactive'))?->getColor() ?? 'gray')
                                    ->formatStateUsing(fn($state): string => StatusData::tryFrom(($state ? 'active' : 'inactive'))?->getLabel() ?? $state)
                            ])
                    ]),
            ]);
    }
}
