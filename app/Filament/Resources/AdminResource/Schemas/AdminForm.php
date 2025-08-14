<?php

namespace App\Filament\Resources\AdminResource\Schemas;

use App\Models\User;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminForm
{
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Data Utama')
                    ->columns()
                    ->schema([
                        Select::make('roles')
                            ->label('Role')
                            ->placeholder('Pilih role')
                            ->relationship('roles', 'name', fn(Builder $query) => $query->where('guard_name', 'web')->whereIn('name', ['super_admin', 'admin']))
                            ->required()
                            ->rules([
                                Rule::exists('roles', 'id')->where('guard_name', 'web'),
                            ])
                            ->native(false),

                        TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->minLength(3)
                            ->placeholder('Masukkan nama lengkap'),

                        TextInput::make('email')
                            ->required()
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->placeholder('Masukkan email'),

                        Group::make()
                            ->relationship('userProfile')
                            ->schema([
                                TextInput::make('whatsapp_number')
                                    ->label('No. WhatsApp')
                                    ->minLength(10)
                                    ->maxLength(13)
                                    ->placeholder('Masukkan nomor WhatsApp'),
                            ]),

                        ToggleButtons::make('is_active')
                            ->label('Status Aktif')
                            ->options([
                                true => 'Aktif',
                                false => 'Tidak Aktif',
                            ])
                            ->colors(fn(?User $record): array => $record?->is_active ? ['danger'] : ['primary'])
                            ->default(true)
                            ->inline()
                            ->visible(fn(?User $record) =>
                                $record?->id !== null
                                && $record?->roles instanceof Collection
                                && $record->roles->contains(fn($role) => $role->name !== 'super_admin')
                            ),
                    ]),

                Section::make('Keamanan')
                    ->columns()
                    ->schema([
                        TextInput::make('password')
                            ->label(fn($livewire) => $livewire instanceof EditRecord ? 'Kata Sandi Baru' : 'Kata Sandi')
                            ->placeholder('Masukkan kata sandi')
                            ->password()
                            ->confirmed()
                            ->minLength(8)
                            ->regex('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/')
                            ->maxLength(20)
                            ->autocomplete('new-password')
                            ->dehydrated(fn(?string $state): bool => filled($state))
                            ->required(fn(string $operation): bool => $operation === 'create')
                            ->helperText('Kata sandi harus terdiri dari minimal 8 karakter, termasuk huruf besar, huruf kecil, angka, dan simbol.')
                            ->dehydrateStateUsing(fn($state) => filled($state) ? Hash::make($state) : null)
                            ->revealable(),

                        TextInput::make('password_confirmation')
                            ->label('Konfirmasi Kata Sandi')
                            ->placeholder('Masukkan konfirmasi kata sandi')
                            ->password()
                            ->minLength(8)
                            ->maxLength(20)
                            ->autocomplete('new-password')
                            ->dehydrated(fn(?string $state): bool => filled($state))
                            ->required(fn(string $operation): bool => $operation === 'create')
                            ->helperText('Ketik ulang kata sandi untuk konfirmasi.')
                            ->dehydrateStateUsing(fn($state) => filled($state) ? Hash::make($state) : null)
                            ->revealable(),
                    ]),

                Grid::make()
                    ->columns()
                    ->visible(fn(?User $record): bool => $record?->exists() ?? false)
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
