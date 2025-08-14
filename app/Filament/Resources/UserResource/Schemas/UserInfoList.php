<?php

namespace App\Filament\Resources\UserResource\Schemas;

use Afsakar\LeafletMapPicker\LeafletMapPickerEntry;
use App\Enums\AccountType;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;

class UserInfoList
{
    public static function configure(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Group::make()
                    ->schema([
                        Section::make('Profile')
                            ->inlineLabel()
                            ->schema([
                                TextEntry::make('userProfile.account_type')
                                    ->label('Tipe Akun')
                                    ->badge()
                                    ->formatStateUsing(fn($state): string => AccountType::tryFrom($state)?->getLabel() ?? 'Unknown')
                                    ->icon(fn($state): string => AccountType::tryFrom($state)?->getIcon() ?? 'heroicon-o-question-mark-circle')
                                    ->color(fn($state): string => AccountType::tryFrom($state)?->getColor() ?? 'gray'),

                                TextEntry::make('name')
                                    ->label('Nama'),

                                TextEntry::make('email')
                                    ->label('Email'),

                                TextEntry::make('userProfile.whatsapp_number')
                                    ->label('WhatsApp Number'),

                                TextEntry::make('roles.name')
                                    ->label('Role'),

                                TextEntry::make('is_active')
                                    ->label('Aktif')
                                    ->badge()
                                    ->formatStateUsing(fn($state): string => $state ? 'Ya' : 'Tidak')
                                    ->icon(fn($state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                                    ->color(fn($state): string => $state ? 'success' : 'danger'),
                            ]),

                        Section::make('Address')
                            ->inlineLabel()
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
                    ]),

                Group::make()
                    ->schema([
                        Tabs::make()
                            ->columnSpanFull()
                            ->tabs([
                                Tab::make('Lokasi Peta')
                                    ->icon('heroicon-o-map')
                                    ->schema([
                                        LeafletMapPickerEntry::make('userProfile.lat_long')
                                            ->hiddenLabel()
                                            ->tileProvider('google')
                                            ->hideTileControl()
                                    ]),

                                Tab::make('Foto Tempat Tinggal')
                                    ->icon('heroicon-o-photo')
                                    ->schema([
                                        Group::make()
                                            ->relationship('userProfile')
                                            ->schema([
                                                SpatieMediaLibraryImageEntry::make('home_photos')
                                                    ->hiddenLabel()
                                                    ->collection('home_photos')
                                                    ->disk('s3')
                                                    ->visibility('private')
                                                    ->openUrlInNewTab()
                                            ]),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}