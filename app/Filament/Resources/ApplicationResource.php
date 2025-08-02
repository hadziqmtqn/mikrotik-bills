<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApplicationResource\Pages;
use App\Models\Application;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ApplicationResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Application::class;
    protected static ?string $slug = 'applications';
    protected static ?string $navigationLabel = 'Aplikasi';
    protected static ?string $navigationGroup = 'Pengaturan';
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
