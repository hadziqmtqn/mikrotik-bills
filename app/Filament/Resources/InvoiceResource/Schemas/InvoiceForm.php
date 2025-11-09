<?php

namespace App\Filament\Resources\InvoiceResource\Schemas;

use App\Enums\AccountType;
use App\Services\UserService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;

class InvoiceForm
{
    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Section::make('Data Pelanggan')
                            ->schema([
                                ToggleButtons::make('account_type')
                                    ->label('Jenis Pelanggan')
                                    ->options(AccountType::options())
                                    ->inline()
                                    ->required()
                                    ->dehydrated(false)
                                    ->reactive()
                                    ->afterStateUpdated(function (callable $set): void {
                                        $set('user_id', null);
                                    }),

                                Select::make('user_id')
                                    ->label('Pelanggan')
                                    ->options(function (Get $get): array {
                                        $accountType = $get('account_type');

                                        if (!$accountType) return [];

                                        return UserService::dropdownOptions(accountType: $accountType);
                                    })
                                    ->required()
                                    ->native(false)
                                    ->reactive()
                            ]),

                        Section::make('Masa Faktur')
                            ->columns()
                            ->schema([
                                DatePicker::make('date')
                                    ->label('Tanggal')
                                    ->native(false)
                                    ->default(now())
                                    ->required()
                                    ->placeholder('Masukkan tanggal faktur')
                                    ->closeOnDateSelection(),

                                DatePicker::make('due_date')
                                    ->label('Tanggal Jatuh Tempo')
                                    ->native(false)
                                    ->required()
                                    ->default(now()->setDay(20))
                                    ->maxDate(now()->endOfMonth())
                                    ->placeholder('Masukkan tanggal jatuh tempo')
                                    ->closeOnDateSelection(),
                            ]),
                    ]),

                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        Section::make('Ringkasan')
                            ->schema([

                            ])
                    ]),
            ]);
    }
}
