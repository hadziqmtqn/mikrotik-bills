<?php

namespace App\Filament\Resources\UserResource\Schemas;

use Afsakar\LeafletMapPicker\LeafletMapPicker;
use App\Models\User;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

class UserForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('Profile')
                            ->icon('heroicon-o-user')
                            ->columns()
                            ->schema([
                                Group::make()
                                    ->relationship('userProfile')
                                    ->schema([
                                        ToggleButtons::make('account_type')
                                            ->label('Tipe Akun')
                                            ->columnSpanFull()
                                    ]),

                                Select::make('roles')
                                    ->label('Role')
                                    ->placeholder('Pilih role')
                                    ->prefixIcon('heroicon-o-shield-check')
                                    ->relationship('roles', 'name')
                                    ->preload()
                                    ->required()
                                    ->rules([
                                        Rule::exists('roles', 'id')->where('guard_name', 'web'),
                                    ])
                                    ->searchable()
                                    ->native(false),

                                TextInput::make('name')
                                    ->prefixIcon('heroicon-o-user-circle')
                                    ->placeholder('Masukkan nama lengkap')
                                    ->required(),

                                TextInput::make('email')
                                    ->placeholder('Masukkan alamat email')
                                    ->prefixIcon('heroicon-o-envelope')
                                    ->email()
                                    ->unique(ignoreRecord: true)
                                    ->required(),

                                Group::make()
                                    ->relationship('userProfile')
                                    ->schema([
                                        TextInput::make('whatsapp_number')
                                            ->placeholder('Masukkan nomor WhatsApp')
                                            ->prefixIcon('heroicon-o-phone')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->rules([
                                                'regex:/^(08|628)[0-9]{8,11}$/',
                                            ]),
                                    ]),

                                Radio::make('is_active')
                                    ->label('Status')
                                    ->inline()
                                    ->options([
                                        true => 'Active',
                                        false => 'Inactive',
                                    ])
                                    ->default(true)
                                    ->required(),
                            ]),

                        Tabs\Tab::make('Address')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                Group::make()
                                    ->relationship('userProfile')
                                    ->columns()
                                    ->schema([
                                        TextInput::make('place_name')
                                            ->label('Nama Tempat')
                                            ->placeholder('Masukkan nama tempat')
                                            ->maxLength(100)
                                            ->dehydrated()
                                            ->dehydrateStateUsing(fn($state) => $state === '' ? null : $state),

                                        Select::make('province')
                                            ->label('Provinsi')
                                            ->placeholder('Pilih provinsi')
                                            ->searchable()
                                            ->getSearchResultsUsing(function (string $search) {
                                                $response = Http::get('https://idn-location.bkn.my.id/api/v1/provinces', [
                                                    'q' => $search,
                                                ]);
                                                return collect($response->json())->pluck('name', 'name')->toArray();
                                            })
                                            ->getOptionLabelUsing(fn ($value) => $value)
                                            ->dehydrated()
                                            ->dehydrateStateUsing(fn($state) => $state === '' ? null : $state)
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                $set('city', null);
                                                $set('district', null);
                                                $set('village', null);
                                            }),

                                        Select::make('city')
                                            ->label('Kota/Kabupaten')
                                            ->placeholder('Pilih kota/kabupaten')
                                            ->searchable()
                                            ->getSearchResultsUsing(function (string $search, $get) {
                                                $province = $get('province');
                                                if (!$province) return [];
                                                $response = Http::get('https://idn-location.bkn.my.id/api/v1/cities', [
                                                    'province' => $province,
                                                    'q' => $search,
                                                ]);
                                                return collect($response->json())->pluck('name', 'name')->toArray();
                                            })
                                            ->getOptionLabelUsing(fn ($value) => $value)
                                            ->dehydrated()
                                            ->dehydrateStateUsing(fn($state) => $state === '' ? null : $state)
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                $set('district', null);
                                                $set('village', null);
                                            }),

                                        Select::make('district')
                                            ->label('Kecamatan')
                                            ->placeholder('Pilih kecamatan')
                                            ->searchable()
                                            ->getSearchResultsUsing(function (string $search, $get) {
                                                $city = $get('city');
                                                if (!$city) return [];
                                                $response = Http::get('https://idn-location.bkn.my.id/api/v1/districts', [
                                                    'city' => $city,
                                                    'q' => $search,
                                                ]);
                                                return collect($response->json())->pluck('name', 'name')->toArray();
                                            })
                                            ->getOptionLabelUsing(fn ($value) => $value)
                                            ->dehydrated()
                                            ->dehydrateStateUsing(fn($state) => $state === '' ? null : $state)
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                $set('village', null);
                                            }),

                                        Select::make('village')
                                            ->label('Desa/Kelurahan')
                                            ->placeholder('Pilih desa/kelurahan')
                                            ->searchable()
                                            ->getSearchResultsUsing(function (string $search, $get) {
                                                $district = $get('district');
                                                if (!$district) return [];
                                                $response = Http::get('https://idn-location.bkn.my.id/api/v1/villages', [
                                                    'district' => $district,
                                                    'q' => $search,
                                                ]);
                                                return collect($response->json())->pluck('name', 'name')->toArray();
                                            })
                                            ->getOptionLabelUsing(fn ($value) => $value)
                                            ->dehydrated()
                                            ->dehydrateStateUsing(fn($state) => $state === '' ? null : $state)
                                            ->reactive(),

                                        TextInput::make('street')
                                            ->label('Jalan')
                                            ->placeholder('Masukkan nama jalan')
                                            ->maxLength(255)
                                            ->dehydrated()
                                            ->dehydrateStateUsing(fn($state) => $state === '' ? null : $state),

                                        TextInput::make('postal_code')
                                            ->label('Kode Pos')
                                            ->placeholder('Masukkan kode pos')
                                            ->maxLength(10)
                                            ->dehydrated()
                                            ->dehydrateStateUsing(fn($state) => $state === '' ? null : $state),

                                        LeafletMapPicker::make('lat_long')
                                            ->label('Select Location')
                                            ->tileProvider('google')
                                            ->draggable(false)
                                            ->clickable()
                                            ->defaultZoom(15)
                                            ->columnSpanFull()
                                            ->afterStateUpdated(function (Set $set, ?array $state): void {
                                                if (!$state) {
                                                    $set('latitude', null);
                                                    $set('longitude', null);
                                                    return;
                                                }

                                                $set('latitude', $state['lat']);
                                                $set('longitude', $state['lng']);
                                            })
                                            ->afterStateHydrated(function (Set $set, ?array $state): void {
                                                if ($state) {
                                                    $set('latitude', $state['lat']);
                                                    $set('longitude', $state['lng']);
                                                }
                                            })
                                    ])
                            ]),

                        Tabs\Tab::make('Security')
                            ->icon('heroicon-o-lock-closed')
                            ->columns()
                            ->schema([
                                TextInput::make('password')
                                    ->label(fn($livewire) => $livewire instanceof EditRecord ? 'Kata Sandi Baru' : 'Kata Sandi')
                                    ->placeholder('Masukkan kata sandi')
                                    ->password()
                                    ->confirmed()
                                    ->minLength(8)
                                    ->regex('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/')
                                    ->maxLength(255)
                                    ->autocomplete('new-password')
                                    ->dehydrated(fn (?string $state): bool => filled($state))
                                    ->required(fn (string $operation): bool => $operation === 'create')
                                    ->helperText('Kata sandi harus terdiri dari minimal 8 karakter, termasuk huruf besar, huruf kecil, angka, dan simbol.')
                                    ->dehydrateStateUsing(fn($state) => filled($state) ? Hash::make($state) : null)
                                    ->revealable(),

                                TextInput::make('password_confirmation')
                                    ->label('Konfirmasi Kata Sandi')
                                    ->placeholder('Masukkan konfirmasi kata sandi')
                                    ->password()
                                    ->minLength(8)
                                    ->maxLength(255)
                                    ->autocomplete('new-password')
                                    ->dehydrated(fn (?string $state): bool => filled($state))
                                    ->required(fn (string $operation): bool => $operation === 'create')
                                    ->helperText('Ketik ulang kata sandi untuk konfirmasi.')
                                    ->dehydrateStateUsing(fn($state) => filled($state) ? Hash::make($state) : null)
                                    ->revealable(),
                            ]),
                    ]),

                Grid::make()
                    ->schema([
                        Placeholder::make('created_at')
                            ->label('Created Date')
                            ->visible(fn(?User $record): bool => $record?->exists() ?? false)
                            ->content(fn(?User $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                        Placeholder::make('updated_at')
                            ->label('Last Modified Date')
                            ->visible(fn(?User $record): bool => $record?->exists() ?? false)
                            ->content(fn(?User $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
                    ]),
            ]);
    }
}