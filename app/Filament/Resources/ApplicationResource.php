<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApplicationResource\Pages;
use App\Models\Application;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ApplicationResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Application::class;
    protected static ?string $slug = 'applications';
    protected static ?string $navigationLabel = 'Aplikasi';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    public static function getPermissionPrefixes(): array
    {
        // TODO: Implement getPermissionPrefixes() method.
        return [
            'view_any',
            'view',
            'create',
            'update',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('short_name')
                    ->required(),

                TextInput::make('full_name'),

                Select::make('panel_color')
                    ->label('Warna Panel')
                    ->options(
                        collect(Color::all())
                            // Ubah key jadi label yang lebih enak dibaca (opsional)
                            ->mapWithKeys(function ($value, $key) {
                                // Translate label sesuai kebutuhan
                                $labels = [
                                    'slate' => 'Abu Tua',
                                    'gray' => 'Abu',
                                    'zinc' => 'Zinc',
                                    'neutral' => 'Netral',
                                    'stone' => 'Stone',
                                    'red' => 'Merah',
                                    'orange' => 'Oranye',
                                    'amber' => 'Amber',
                                    'yellow' => 'Kuning',
                                    'lime' => 'Lime',
                                    'green' => 'Hijau',
                                    'emerald' => 'Emerald',
                                    'teal' => 'Teal',
                                    'cyan' => 'Cyan',
                                    'sky' => 'Biru Langit',
                                    'blue' => 'Biru',
                                    'indigo' => 'Indigo',
                                    'violet' => 'Ungu',
                                    'purple' => 'Ungu Tua',
                                    'fuchsia' => 'Fuchsia',
                                    'pink' => 'Merah Muda',
                                    'rose' => 'Rose',
                                ];
                                return [$key => $labels[$key] ?? ucfirst($key)];
                            })
                            ->toArray()
                    )
                    ->native(false)
                    ->searchable()
                    ->helperText('Warna panel ini akan digunakan sebagai tema utama aplikasi Anda.')
                    ->required(),

                Select::make('navigation_position')
                    ->label('Posisi Navigasi')
                    ->options([
                        'top' => 'Atas',
                        'left' => 'Kiri',
                    ])
                    ->default('top')
                    ->native(false)
                    ->searchable()
                    ->helperText('Posisi navigasi ini akan menentukan di mana menu aplikasi ditampilkan pada antarmuka pengguna.')
                    ->required(),

                SpatieMediaLibraryFileUpload::make('favicon')
                    ->label('Favicon')
                    ->collection('favicon')
                    ->image()
                    ->disk('s3')
                    ->maxSize(50)
                    ->visibility('private')
                    ->openable()
                    ->dehydrated(fn($state) => filled($state))
                    ->columnSpanFull(),

                SpatieMediaLibraryFileUpload::make('invoice_logo')
                    ->label('Logo Invoice')
                    ->collection('invoice_logo')
                    ->image()
                    ->maxSize(150)
                    ->openable()
                    ->dehydrated(fn($state) => filled($state))
                    ->columnSpanFull(),

                Grid::make()
                    ->columns()
                    ->schema([
                        Placeholder::make('created_at')
                            ->label('Created Date')
                            ->content(fn(?Application $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                        Placeholder::make('updated_at')
                            ->label('Last Modified Date')
                            ->content(fn(?Application $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('favicon')
                    ->label('Favicon')
                    ->collection('favicon')
                    ->disk('s3')
                    ->visibility('private')
                    ->circular()
                    ->size(32),

                TextColumn::make('short_name')
                    ->label('Nama Singkat')
                    ->searchable(),

                TextColumn::make('full_name')
                    ->label('Nama Lengkap')
                    ->searchable(),

                TextColumn::make('panel_color')
                    ->label('Warna Panel')
                    ->formatStateUsing(fn($state): string => ucfirst($state)),

                TextColumn::make('navigation_position')->formatStateUsing(fn($state): string => ucfirst($state))
                    ->label('Posisi Navigasi'),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApplications::route('/'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['short_name', 'full_name'];
    }

    public static function canCreate(): bool
    {
        return Application::count() === 0;
    }
}
