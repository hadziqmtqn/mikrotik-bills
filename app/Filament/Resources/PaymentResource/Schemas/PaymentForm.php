<?php

namespace App\Filament\Resources\PaymentResource\Schemas;

use App\Enums\PaymentMethod;
use App\Enums\StatusData;
use App\Models\Invoice;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Builder;

class PaymentForm
{
    public static function form(Form $form, $invoiceId = null): Form
    {
        $invoice = Invoice::find($invoiceId);

        return $form
            ->schema([
                Section::make()
                    ->columns()
                    ->schema([
                        TextInput::make('amount')
                            ->label('Jumlah Pembayaran')
                            ->numeric()
                            ->prefix('Rp')
                            ->maxValue(fn(): ?int => $invoice?->total_price)
                            ->required()
                            ->afterStateHydrated(function ($state, $set) use ($invoice) {
                                if (blank($state)) {
                                    $set('amount', $invoice?->total_price);
                                }
                            })
                            ->afterStateUpdated(function ($state, $set) use ($invoice) {
                                if (blank($state)) {
                                    // Kosongkan status jika amount kosong
                                    $set('status', null);
                                } elseif ($state < $invoice?->total_price) {
                                    $set('status', 'partially_paid');
                                } else {
                                    $set('status', 'paid');
                                }
                            })
                            ->reactive()
                            ->placeholder('Masukkan Jumlah Pembayaran'),

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
                            ->minDate($invoice?->date)
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
                            ->options(StatusData::options(['partially_paid', 'paid', 'cancelled']))
                            ->colors(StatusData::colors(['partially_paid', 'paid', 'cancelled']))
                            ->default('paid')
                            ->required()
                    ]),
            ]);
    }
}
