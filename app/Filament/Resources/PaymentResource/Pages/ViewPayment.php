<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;

class ViewPayment
{
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
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
                    ])
            ]);
    }
}
