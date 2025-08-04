<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServicePackageResource\Pages;
use App\Models\Router;
use App\Models\ServicePackage;
use Exception;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
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
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServicePackageResource extends Resource
{
    protected static ?string $model = ServicePackage::class;
    protected static ?string $slug = 'service-packages';
    protected static ?string $navigationLabel = 'Paket Layanan';
    protected static ?string $navigationIcon = 'heroicon-o-server-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        // TODO: General Information
                        Section::make()
                            ->columns()
                            ->schema([
                                Radio::make('service_type')
                                    ->label('Tipe Layanan')
                                    ->options([
                                        'hotspot' => 'Hotspot',
                                        'pppoe' => 'PPPoE',
                                    ])
                                    ->inline()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state !== 'hotspot') {
                                            $set('package_limit_type', null);
                                            $set('limit_type', null);
                                            $set('time_limit', null);
                                            $set('time_limit_unit', null);
                                            $set('data_limit', null);
                                            $set('data_limit_unit', null);
                                        }

                                        if ($state !== 'pppoe') {
                                            $set('validity_period', null);
                                            $set('validity_unit', null);
                                        }
                                    })
                                    ->columnSpanFull(),

                                Radio::make('payment_type')
                                    ->label('Tipe Pembayaran')
                                    ->options([
                                        'prepaid' => 'Prepaid',
                                        'postpaid' => 'Postpaid',
                                    ])
                                    ->inline()
                                    ->required()
                                    ->columnSpanFull(),

                                TextInput::make('package_name')
                                    ->label('Nama Paket Layanan')
                                    ->required()
                                    ->columnSpanFull(),

                                Select::make('plan_type')
                                    ->label('Tipe Paket')
                                    ->hintIcon('heroicon-o-information-circle', 'Pilih tipe paket layanan untuk jenis pelanggan yang sesuai.')
                                    ->options([
                                        'pribadi' => 'Pribadi',
                                        'bisnis' => 'Bisnis',
                                    ])
                                    ->native(false)
                                    ->required(),

                                Select::make('router_id')
                                    ->label('Router')
                                    ->options(fn() => Router::where('is_active', true)->get()->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->required(),
                            ]),

                        // TODO: Hotspot settings
                        Section::make('Hotspot Settings')
                            ->description('Pengaturan khusus untuk paket layanan Hotspot.')
                            ->hidden(fn(Get $get) => $get('service_type') !== 'hotspot')
                            ->columns()
                            ->schema([
                                Select::make('package_limit_type')
                                    ->label('Tipe Batasan Paket')
                                    ->options([
                                        'unlimited' => 'Tidak Terbatas',
                                        'limited' => 'Terbatas',
                                    ])
                                        ->native(false)
                                        ->required(fn(Get $get) => $get('service_type') === 'hotspot')
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, callable $set) {
                                            if ($state !== 'limited') {
                                                $set('limit_type', null);
                                                $set('time_limit', null);
                                                $set('time_limit_unit', null);
                                                $set('data_limit', null);
                                                $set('data_limit_unit', null);
                                            }
                                        })
                                        ->columnSpanFull(),

                                Radio::make('limit_type')
                                    ->label('Tipe Batasan')
                                    ->options([
                                        'time' => 'Waktu',
                                        'data' => 'Data',
                                        'both' => 'Waktu & Data',
                                    ])
                                    ->inline()
                                    ->required(fn(Get $get) => $get('package_limit_type') === 'limited')
                                    ->hidden(fn(Get $get) => $get('package_limit_type') !== 'limited')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state !== 'time') {
                                            $set('time_limit', null);
                                            $set('time_limit_unit', null);
                                        }

                                        if ($state !== 'data') {
                                            $set('data_limit', null);
                                            $set('data_limit_unit', null);
                                        }

                                        if ($state !== 'both') {
                                            $set('time_limit', null);
                                            $set('time_limit_unit', null);
                                            $set('data_limit', null);
                                            $set('data_limit_unit', null);
                                        }
                                    })
                                    ->columnSpanFull(),

                                TextInput::make('time_limit')
                                    ->label('Batasan Waktu')
                                    ->hidden(fn(Get $get) => $get('package_limit_type') !== 'limited' || ($get('limit_type') !== 'time' && $get('limit_type') !== 'both'))
                                    ->required(fn(Get $get) => $get('package_limit_type') === 'limited' && ($get('limit_type') === 'time' || $get('limit_type') === 'both'))
                                    ->numeric()
                                    ->integer(),

                                Select::make('time_limit_unit')
                                    ->label('Satuan Batasan Waktu')
                                    ->options([
                                        'menit' => 'Menit',
                                        'jam' => 'Jam',
                                        'hari' => 'Hari',
                                    ])
                                    ->hidden(fn(Get $get) => $get('package_limit_type') !== 'limited' || ($get('limit_type') !== 'time' && $get('limit_type') !== 'both'))
                                    ->required(fn(Get $get) => $get('package_limit_type') === 'limited' && ($get('limit_type') === 'time' || $get('limit_type') === 'both'))
                                    ->default('menit')
                                    ->native(false),

                                TextInput::make('data_limit')
                                    ->label('Batasan Data')
                                    ->hidden(fn(Get $get) => $get('package_limit_type') !== 'limited' || ($get('limit_type') !== 'data' && $get('limit_type') !== 'both'))
                                    ->required(fn(Get $get) => $get('package_limit_type') === 'limited' && ($get('limit_type') === 'data' || $get('limit_type') === 'both'))
                                    ->numeric()
                                    ->integer(),

                                Select::make('data_limit_unit')
                                    ->label('Satuan Batasan Data')
                                    ->options(['MBs', 'GBs'])
                                    ->hidden(fn(Get $get) => $get('package_limit_type') !== 'limited' || ($get('limit_type') !== 'data' && $get('limit_type') !== 'both'))
                                    ->required(fn(Get $get) => $get('package_limit_type') === 'limited' && ($get('limit_type') === 'data' || $get('limit_type') === 'both'))
                                    ->native(false),
                            ]),

                        // TODO: PPPoE settings
                        Section::make('PPPeE Settings')
                            ->description('Pengaturan khusus untuk paket layanan PPPoE.')
                            ->hidden(fn(Get $get) => $get('service_type') !== 'pppoe')
                            ->columns()
                            ->schema([
                                TextInput::make('validity_period')
                                    ->label('Masa Berlaku Paket')
                                    ->hidden(fn(Get $get) => $get('service_type') !== 'pppoe')
                                    ->required(fn(Get $get) => $get('service_type') === 'pppoe')
                                    ->numeric()
                                    ->integer(),

                                Select::make('validity_unit')
                                    ->label('Satuan Masa Berlaku')
                                    ->options([
                                        'menit' => 'Menit',
                                        'jam' => 'Jam',
                                        'hari' => 'Hari',
                                        'bulan' => 'Bulan',
                                    ])
                                    ->hidden(fn(Get $get) => $get('service_type') !== 'pppoe')
                                    ->required(fn(Get $get) => $get('service_type') === 'pppoe')
                                    ->default('hari')
                                    ->native(false),
                            ]),

                        // TODO: Package Price
                        Section::make('Package Price')
                            ->description('Pengaturan harga paket layanan.')
                            ->columns()
                            ->schema([
                                TextInput::make('package_price')
                                    ->label('Harga Paket Layanan')
                                    ->numeric()
                                    ->required()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->helperText('Harga paket layanan ini.'),

                                TextInput::make('price_before_discount')
                                    ->label('Harga Sebelum Diskon')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->helperText('Harga sebelum diskon, jika ada.'),
                            ]),

                        // TODO: Description
                        Section::make()
                            ->schema([
                                RichEditor::make('description')
                                    ->label('Deskripsi Paket Layanan')
                                    ->fileAttachmentsDisk('s3')
                                    ->fileAttachmentsDirectory('attachments')
                                    ->fileAttachmentsVisibility('private')
                            ])
                    ])->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make('Status')
                            ->schema([
                                Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->default(true)
                                    ->inline()
                                    ->required()
                                    ->helperText('Aktifkan paket layanan ini untuk membuatnya tersedia bagi pelanggan.')
                                    ->columnSpanFull(),

                                Placeholder::make('created_at')
                                    ->label('Created Date')
                                    ->content(fn(?ServicePackage $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                                Placeholder::make('updated_at')
                                    ->label('Last Modified Date')
                                    ->content(fn(?ServicePackage $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
                            ])
                    ])->columnSpan(['lg' => 1]),
            ])
                ->columns(3);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('slug')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('serial_number'),

                TextColumn::make('code'),

                TextColumn::make('service_type'),

                TextColumn::make('package_name'),

                TextColumn::make('payment_type'),

                TextColumn::make('plan_type'),

                TextColumn::make('package_limit_type'),

                TextColumn::make('limit_type'),

                TextColumn::make('time_limit'),

                TextColumn::make('time_limit_unit'),

                TextColumn::make('data_limit'),

                TextColumn::make('data_limit_unit'),

                TextColumn::make('validity_period'),

                TextColumn::make('validity_unit'),

                TextColumn::make('package_price'),

                TextColumn::make('price_before_discount'),

                TextColumn::make('router_id'),

                TextColumn::make('description'),

                TextColumn::make('is_active'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
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
            'index' => Pages\ListServicePackages::route('/'),
            'create' => Pages\CreateServicePackage::route('/create'),
            'edit' => Pages\EditServicePackage::route('/{record}/edit'),
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
        return ['slug'];
    }
}
