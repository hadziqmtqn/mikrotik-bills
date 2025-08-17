<?php

namespace App\Filament\Resources\PaymentResource\Schemas;

use App\Enums\PaymentMethod;
use App\Enums\StatusData;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Builder;

class PaymentForm
{
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns()
                    ->schema([
                        ToggleButtons::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->required()
                            ->inline()
                            ->options(PaymentMethod::options())
                            ->colors(PaymentMethod::colors())
                            ->icons(PaymentMethod::icons())
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('bank_account_id', null);
                            })
                            ->columnSpanFull(),

                        Select::make('bank_account_id')
                            ->label('Rekening Bank')
                            ->relationship('bankAccount', 'short_name', function (Builder $query) {
                                return $query->where('is_active', true);
                            })
                            ->native(false)
                            ->placeholder('Pilih Tujuan Bank')
                            ->required(fn(Get $get): bool => $get('payment_method') === 'bank_transfer')
                            ->disabled(fn(Get $get): bool => $get('payment_method') !== 'bank_transfer'),

                        DatePicker::make('date')
                            ->label('Tanggal Pembayaran')
                            ->required()
                            ->native(false)
                            ->maxDate(now())
                            ->placeholder('Tanggal Pembayaran')
                            ->closeOnDateSelection(),
                    ]),

                Section::make('Bukti Pembayaran')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('proof_of_payment')
                            ->hiddenLabel()
                            ->required(fn(Get $get): bool => $get('payment_method') === PaymentMethod::BANK_TRANSFER->value)
                            ->disk('s3')
                            ->collection('proof_of_payment')
                            ->visibility('private')
                            ->openable()
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                    ]),

                Section::make('Catatan')
                    ->schema([
                        Textarea::make('notes')
                            ->hiddenLabel()
                            ->placeholder('Masukkan catatan')
                            ->autosize()
                            ->columnSpanFull()
                    ]),

                Section::make('Status Pembayaran')
                    ->schema([
                        ToggleButtons::make('status')
                            ->hiddenLabel()
                            ->inline()
                            ->options(StatusData::options(['paid', 'cancelled']))
                            ->colors(StatusData::colors(['paid', 'cancelled']))
                            ->default('paid')
                            ->required()
                    ]),
            ]);
    }
}
