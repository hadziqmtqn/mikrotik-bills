<?php

namespace App\Filament\Clusters\Reference\Resources\ExtraCostResource\Schemas;

use App\Enums\BillingType;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class ExtraCostForm
{
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->placeholder('Nama Biaya Tambahan'),

                        TextInput::make('fee')
                            ->label('Biaya')
                            ->prefix('Rp')
                            ->numeric()
                            ->required()
                            ->placeholder('Biaya Tambahan'),

                        Radio::make('billing_type')
                            ->label('Jenis Tagihan')
                            ->options(BillingType::options())
                            ->required()
                            ->inline(),

                        Textarea::make('note')
                            ->label('Catatan')
                            ->placeholder('Masukkan catatan biaya tambahan')
                            ->autosize()
                    ])
            ]);
    }
}
