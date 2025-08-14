<?php

namespace App\Filament\Resources\AdminResource\Schemas;

use App\Models\User;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Builder;

class AdminForm
{
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Data Utama')
                    ->columns()
                    ->schema([
                        Select::make('role')
                            ->label('Role')
                            ->relationship('roles', 'name', fn(Builder $query) => $query->whereIn('name', ['super_admin', 'admin']))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false),

                        TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->minLength(3)
                            ->placeholder('Masukkan nama lengkap'),

                        TextInput::make('email')
                            ->required()
                            ->email()
                            ->unique(User::class, 'email', fn(?User $record) => $record?->id)
                            ->placeholder('Masukkan email'),

                        ToggleButtons::make('is_active')
                            ->label('Status Aktif')
                            ->options([
                                true => 'Aktif',
                                false => 'Tidak Aktif',
                            ])
                            ->colors(fn(?User $record): array => $record?->is_active ? ['danger'] : ['primary'])
                            ->default(true)
                            ->inline()
                            ->visible(fn(?User $record): bool => $record?->id !== null && $record?->roles?->contains('name', '!=', 'super_admin')),
                    ]),

                Section::make('Keamanan')
                    ->columns()
                    ->schema([
                        TextInput::make('password')
                            ->label('Kata Sandi')
                            ->password()
                            ->minLength(8)
                            ->required(fn(?User $record): bool => $record?->id === null)
                            ->regex('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/')
                            ->placeholder('Masukkan kata sandi baru')
                            ->helperText(fn(?User $record): string => $record?->id === null ? 'Kata sandi harus terdiri dari minimal 8 karakter.' : 'Biarkan kosong jika tidak ingin mengubah kata sandi.')
                            ->revealable(),

                        TextInput::make('password_confirmation')
                            ->label('Konfirmasi Kata Sandi')
                            ->password()
                            ->same('password')
                            ->required(fn(?User $record): bool => $record?->id === null)
                            ->placeholder('Konfirmasi kata sandi baru')
                            ->helperText(fn(?User $record): string => $record?->id === null ? 'Kata sandi harus terdiri dari minimal 8 karakter.' : 'Biarkan kosong jika tidak ingin mengubah kata sandi.')
                            ->revealable(),
                    ]),

                Grid::make()
                    ->columns()
                    ->visible(fn(?User $record): bool => $record?->exists ?? false)
                    ->schema([
                        Placeholder::make('created_at')
                            ->label('Created Date')
                            ->content(fn(?User $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                        Placeholder::make('updated_at')
                            ->label('Last Modified Date')
                            ->content(fn(?User $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
                    ]),
            ]);
    }
}
