<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Enums\PaymentMethod;
use App\Enums\StatusData;
use App\Filament\Resources\InvoiceResource;
use App\Helpers\DateHelper;
use App\Models\Payment;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Illuminate\Support\HtmlString;

class ViewPayment
{
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->columns(3)
            ->schema([
                Group::make()
                    ->schema([
                        Section::make('Data Pelanggan')
                            ->inlineLabel()
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Nama'),

                                TextEntry::make('user.userProfile.whatsapp_number')
                                    ->label('No. Whatsapp'),

                                TextEntry::make('user.email')
                                    ->label('Email'),
                            ]),

                        Section::make('Pembayaran')
                            ->inlineLabel()
                            ->collapsed()
                            ->schema([
                                TextEntry::make('date')
                                    ->label('Tanggal')
                                    ->date()
                                    ->formatStateUsing(fn($state): string => DateHelper::indonesiaDate($state)),

                                TextEntry::make('amount')
                                    ->label('Jumlah')
                                    ->money('idr')
                                    ->weight(FontWeight::Bold),

                                TextEntry::make('payment_method')
                                    ->label('Metode Bayar')
                                    ->formatStateUsing(fn($state): string => PaymentMethod::tryFrom($state)->getLabel()),

                                TextEntry::make('bankAccount.bank_name')
                                    ->label('Bank Tujuan')
                            ]),

                        Section::make('Faktur')
                            ->inlineLabel()
                            ->collapsed()
                            ->schema([
                                TextEntry::make('code')
                                    ->label('Kode Faktur')
                                    ->color('primary')
                                    ->icon('heroicon-s-arrow-top-right-on-square')
                                    ->iconPosition(IconPosition::After)
                                    ->url(fn(Payment $record): string => InvoiceResource::getUrl('view', ['record' => $record->invoice?->slug])),

                                RepeatableEntry::make('invoice.invoiceItems')
                                    ->label('Item')
                                    ->schema([
                                        TextEntry::make('customerService.servicePackage.package_name')
                                            ->label('Paket')
                                    ])
                            ])
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                TextEntry::make('status')
                                    ->formatStateUsing(fn ($state): string => StatusData::tryFrom($state)->getLabel())
                                    ->color(fn($state): string => StatusData::tryFrom($state)->getColor())
                                    ->size(TextEntry\TextEntrySize::Large)
                                    ->weight(FontWeight::Bold),

                                TextEntry::make('proof_of_payment')
                                    ->label('Bukti Pembayaran')
                                    ->color('primary')
                                    ->formatStateUsing(fn($state): HtmlString => new HtmlString('<a href="' . $state . '" target="_blank">Lihat Bukti</a>'))
                                    ->icon('heroicon-s-arrow-top-right-on-square')
                                    ->iconPosition(IconPosition::After),
                            ])
                    ])
                    ->columnSpan(['lg' => 1]),
            ]);
    }
}
