<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApplicationResource\Pages;
use App\Models\Application;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;

class ApplicationResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Application::class;
    protected static ?string $slug = 'applications';
    protected static ?string $navigationLabel = 'Aplikasi';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    public static function getNavigationUrl(): string
    {
        return static::getUrl('edit', ['record' => Application::first()?->getRouteKey()]);
    }

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
                Section::make('Aplikasi')
                    ->description('Pengaturan aplikasi ini akan digunakan untuk mengonfigurasi nama, warna, dan logo aplikasi Anda.')
                    ->inlineLabel()
                    ->aside()
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
                    ]),

                Section::make('Informasi Bisnis')
                    ->description('Pengaturan ini akan digunakan untuk mengonfigurasi data usaha Anda seperti nama, alamat, dan nomor telepon.')
                    ->inlineLabel()
                    ->aside()
                    ->schema([
                        TextInput::make('business_name')
                            ->label('Nama Usaha')
                            ->required(),

                        TextInput::make('business_phone')
                            ->label('Nomor Telepon Usaha')
                            ->required(),

                        TextInput::make('business_email')
                            ->label('Email Usaha')
                            ->email()
                            ->required(),

                        Textarea::make('business_address')
                            ->label('Alamat Usaha')
                            ->rows(2),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApplications::route('/'),
            'edit' => Pages\EditApplication::route('{record}'),
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
