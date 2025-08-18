<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Helpers\DateHelper;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;

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
                            ->schema([
                                TextEntry::make('date')
                                    ->label('Tanggal')
                                    ->date()
                                    ->formatStateUsing(fn($state): string => DateHelper::indonesiaDate($state))
                            ])
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([

                    ])
            ]);
    }
}
