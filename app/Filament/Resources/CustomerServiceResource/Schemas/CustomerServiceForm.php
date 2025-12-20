<?php

namespace App\Filament\Resources\CustomerServiceResource\Schemas;

use App\Enums\PackageTypeService;
use App\Models\CustomerService;
use App\Models\ServicePackage;
use App\Models\UserProfile;
use App\Services\ExtraCostService;
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
use Illuminate\Database\Eloquent\Builder;

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
                                Select::make('user_id')
                                    ->label('Pelanggan')
                                    ->required()
                                    ->options(function (?CustomerService $record) {
                                        return UserService::dropdownOptions($record?->user_id);
                                    })
                                    ->searchable()
                                    ->reactive()
                                    ->native(false)
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $set('service_package_id', null);
                                        $set('price', null);
                                    }),

                                Grid::make()
                                    ->schema([
                                        Select::make('service_package_id')
                                            ->label('Paket layanan')
                                            ->required()
                                            ->relationship('servicePackage', 'package_name', function (Builder $query, Get $get) {
                                                $accountType = UserProfile::userId($get('user_id'))
                                                    ->first();

                                                // If account type is not found, return an empty query
                                                if (!$accountType) {
                                                    $query->whereRaw('1 = 0'); // No results
                                                }

                                                $query->where('is_active', true);
                                                $query->where('plan_type', $accountType?->account_type);
                                            })
                                            ->preload()
                                            ->searchable()
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
                                            ->placeholder('Masukkan harga layaranan'),
                                    ])
                            ]),

                        Section::make('Biaya Lain')
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
