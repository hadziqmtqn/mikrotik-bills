<?php

namespace App\Filament\Resources\CustomerServiceResource\Schemas;

use App\Enums\AccountType;
use App\Enums\PackageTypeService;
use App\Enums\ServiceType;
use App\Models\CustomerService;
use App\Models\ServicePackage;
use App\Models\UserProfile;
use App\Services\ExtraCostService;
use App\Services\ServicePackageService;
use App\Services\UserService;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;

class CustomerServiceForm
{
    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                ToggleButtons::make('account_type')
                                    ->label('Jenis Pelanggan')
                                    ->options(AccountType::options())
                                    ->required()
                                    ->inline()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set): void {
                                        $set('user_id', []);
                                    }),

                                Select::make('user_id')
                                    ->label('Pelanggan')
                                    ->required()
                                    ->options(function (?CustomerService $record, Get $get) {
                                        $accountType = $get('account_type');

                                        if (!$accountType) {
                                            return [];
                                        }

                                        return UserService::dropdownOptions(
                                            selfId: $record?->user_id,
                                            accountType: $accountType
                                        );
                                    })
                                    ->searchable()
                                    ->reactive()
                                    ->native(false)
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $set('service_package_id', []);
                                        $set('price', null);
                                    }),

                                Grid::make()
                                    ->schema([
                                        ToggleButtons::make('service_type')
                                            ->label('Jenis Layanan')
                                            ->options(ServiceType::options())
                                            ->inline()
                                            ->columnSpanFull()
                                            ->required()
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $set): void {
                                                $set('service_package_id', []);
                                                $set('price', null);
                                            }),

                                        Select::make('service_package_id')
                                            ->label('Paket layanan')
                                            ->required()
                                            ->options(function (Get $get): array {
                                                $accountType = UserProfile::userId($get('user_id'))
                                                    ->first();
                                                $serviceType = $get('service_type');

                                                if (!$accountType || !$serviceType) return [];

                                                return collect(ServicePackageService::options(
                                                    planType: $accountType->account_type,
                                                    serviceType: $serviceType,
                                                    activeOnly: true
                                                ))
                                                    ->map(fn($item) => $item['name'])
                                                    ->toArray();
                                            })
                                            ->reactive()
                                            ->native(false)
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                if ($state) {
                                                    $servicePackage = ServicePackage::find($state);
                                                    if ($servicePackage) {
                                                        $set('price', $servicePackage->package_price);
                                                    }
                                                } else {
                                                    $set('price', null);
                                                }
                                            }),

                                        TextInput::make('price')
                                            ->label('Harga')
                                            ->required()
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->placeholder('Masukkan harga layaranan')
                                            ->readOnly(),
                                    ])
                            ]),

                        Section::make('Biaya Lain')
                            ->visibleOn('create')
                            ->schema([
                                CheckboxList::make('inv_extra_costs')
                                    ->label('Biaya Tambahan')
                                    ->options(collect(ExtraCostService::options())->map(fn($data) => $data['name']))
                                    ->descriptions(collect(ExtraCostService::options())->map(fn($data) => 'Rp' . number_format($data['fee'],0,',','.')))
                                    ->columns()
                                    ->bulkToggleable()
                            ])
                    ])
                    ->columnSpan([
                        'lg' => 2,
                        'md' => 2,
                    ]),

                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                ToggleButtons::make('package_type')
                                    ->label('Jenis Paket')
                                    ->options(PackageTypeService::options())
                                    ->colors(PackageTypeService::colors())
                                    ->required()
                                    ->inline()
                            ])
                    ])
                    ->columnSpan([
                        'lg' => 1,
                        'md' => 1,
                    ]),

                Grid::make()
                    ->visible(fn(?CustomerService $record): bool => $record?->exists ?? false)
                    ->schema([
                        Placeholder::make('created_at')
                            ->label('Created Date')
                            ->content(fn(?CustomerService $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                        Placeholder::make('updated_at')
                            ->label('Last Modified Date')
                            ->content(fn(?CustomerService $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
                    ])
            ]);
    }
}
