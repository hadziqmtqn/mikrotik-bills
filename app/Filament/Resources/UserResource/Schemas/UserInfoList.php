<?php

namespace App\Filament\Resources\UserResource\Schemas;

use Afsakar\LeafletMapPicker\LeafletMapPickerEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;

class UserInfoList
{
    public static function configure(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Profile')
                    ->columns()
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nama'),
                        TextEntry::make('email')
                            ->label('Email'),
                        TextEntry::make('userProfile.whatsapp_number')
                            ->label('WhatsApp Number'),
                        TextEntry::make('roles.name')
                            ->label('Role'),
                    ]),

                Section::make('Address')
                    ->columns()
                    ->schema([
                        TextEntry::make('userProfile.place_name')
                            ->label('Nama Tempat'),
                        TextEntry::make('userProfile.street')
                            ->label('Jalan'),
                        TextEntry::make('userProfile.village')
                            ->label('Desa/Kelurahan'),
                        TextEntry::make('userProfile.district')
                            ->label('Kecamatan'),
                        TextEntry::make('userProfile.city')
                            ->label('Kota/Kabupaten'),
                        TextEntry::make('userProfile.province')
                            ->label('Provinsi'),
                        TextEntry::make('userProfile.postal_code')
                            ->label('Kode Pos'),
                    ]),

                Section::make('Map')
                    ->columnSpanFull()
                    ->schema([
                        LeafletMapPickerEntry::make('userProfile.lat_long')
                            ->hiddenLabel()
                            ->tileProvider('google')
                            ->hideTileControl()
                    ])
            ]);
    }
}