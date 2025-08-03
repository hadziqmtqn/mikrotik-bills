<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Dotswan\MapPicker\Fields\Map;
use Exception;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $slug = 'users';
    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
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
                                Select::make('roles')
                                    ->label('Role')
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
                                    ->required(),

                                TextInput::make('email')
                                    ->prefixIcon('heroicon-o-envelope')
                                    ->email()
                                    ->unique(ignoreRecord: true)
                                    ->required(),

                                Group::make()
                                    ->relationship('userProfile')
                                    ->schema([
                                        TextInput::make('whatsapp_number')
                                            ->label('WhatsApp Number')
                                            ->prefixIcon('heroicon-o-phone')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->rules([
                                                'regex:/^(08|628)[0-9]{8,11}$/',
                                            ]),
                                    ])
                            ]),

                        Tabs\Tab::make('Alamat')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                Group::make()
                                    ->relationship('userProfile')
                                    ->columns()
                                    ->schema([
                                        Select::make('province')
                                            ->label('Provinsi')
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
                                            ->maxLength(255)
                                            ->dehydrated()
                                            ->dehydrateStateUsing(fn($state) => $state === '' ? null : $state),

                                        TextInput::make('postal_code')
                                            ->label('Kode Pos')
                                            ->maxLength(10)
                                            ->dehydrated()
                                            ->dehydrateStateUsing(fn($state) => $state === '' ? null : $state),

                                        Map::make('lat_long')
                                            ->label('Location')
                                            ->columnSpanFull()
                                            // Basic Configuration
                                            ->defaultLocation(latitude: -2.83, longitude: 118.30)
                                            ->draggable()
                                            ->clickable(true) // click to move marker
                                            ->zoom(15)
                                            ->minZoom(0)
                                            ->maxZoom(28)
                                            ->tilesUrl("https://tile.openstreetmap.de/{z}/{x}/{y}.png")
                                            ->detectRetina()
                                            // Marker Configuration
                                            ->showMarker()
                                            ->markerColor("#3b82f6")
                                            ->markerHtml('<div class="custom-marker">...</div>')
                                            ->markerIconUrl(asset('assets/map-pin.svg'))
                                            ->markerIconSize([40, 40])
                                            ->markerIconClassName('my-marker-class')
                                            ->markerIconAnchor([18, 36])
                                            // Controls
                                            ->showFullscreenControl()
                                            ->showZoomControl()
                                            // Location Features
                                            ->liveLocation(true, true)
                                            ->showMyLocationButton()
                                            ->rangeSelectField('distance')
                                            // Extra Customization
                                            ->extraStyles([
                                                'min-height: 40vh',
                                            ])
                                            ->extraControl(['customControl' => true])
                                            ->extraTileControl(['customTileOption' => 'value'])
                                            // State Management
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
                                            }),
                                    ])
                            ]),

                        Tabs\Tab::make('Keamanan')
                            ->icon('heroicon-o-lock-closed')
                            ->columns()
                            ->schema([
                                TextInput::make('password')
                                    ->label(fn($livewire) => $livewire instanceof EditRecord ? 'Kata Sandi Baru' : 'Kata Sandi')
                                    ->password()
                                    ->confirmed()
                                    ->minLength(8)
                                    ->regex('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/')
                                    ->maxLength(255)
                                    ->autocomplete('new-password')
                                    ->dehydrated(fn (?string $state): bool => filled($state))
                                    ->required(fn (string $operation): bool => $operation === 'create')
                                    ->placeholder(fn($livewire) => $livewire instanceof EditRecord ? 'Biarkan kosong jika tidak ingin mengubah password' : null)
                                    ->dehydrateStateUsing(fn($state) => filled($state) ? Hash::make($state) : null)
                                    ->revealable(),

                                TextInput::make('password_confirmation')
                                    ->label('Konfirmasi Kata Sandi')
                                    ->password()
                                    ->minLength(8)
                                    ->maxLength(255)
                                    ->autocomplete('new-password')
                                    ->dehydrated(fn (?string $state): bool => filled($state))
                                    ->required(fn (string $operation): bool => $operation === 'create')
                                    ->placeholder(fn($livewire) => $livewire instanceof EditRecord ? 'Biarkan kosong jika tidak ingin mengubah password' : null)
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

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->description(fn($record): string => $record->email)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('userProfile.whatsapp_number')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('userProfile.street'),

                TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->color(fn($state): string => match ($state) {
                        'super_admin' => 'primary',
                        'admin' => 'info',
                        'user' => 'gray',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn($state): string => ucwords(str_replace('_', ' ', $state)))
                    ->searchable()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('role')
                    ->label('Role')
                    ->options(fn () => Role::all()->pluck('name', 'id'))
                    ->default(Role::where('name', 'user')->first()?->id)
                    ->query(function (Builder $query, array $data) {
                        if ($data['value']) {
                            $query->whereHas('roles', function ($q) use ($data) {
                                $q->where('id', $data['value']);
                            });
                        }
                    })
                    ->native(false),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->hidden(fn(User $record): bool => $record->hasRole('super_admin')),
                RestoreAction::make(),
                ForceDeleteAction::make()
                    ->hidden(fn(User $record): bool => $record->hasRole('super_admin')),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            /*'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),*/
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email', 'userProfile.street', 'userProfile.whatsapp_number'];
    }
}
