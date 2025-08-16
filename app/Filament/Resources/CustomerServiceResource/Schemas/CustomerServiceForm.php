<?php

namespace App\Filament\Resources\CustomerServiceResource\Schemas;

use App\Models\CustomerService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class CustomerServiceForm
{
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('reference_number')
                    ->required(),

                TextInput::make('user_id')
                    ->required()
                    ->integer(),

                TextInput::make('service_package_id')
                    ->required()
                    ->integer(),

                TextInput::make('price')
                    ->required()
                    ->numeric(),

                TextInput::make('payment_type')
                    ->required(),

                TextInput::make('username'),

                TextInput::make('password'),

                DatePicker::make('start_date'),

                DatePicker::make('end_date_time'),

                TextInput::make('status')
                    ->required(),

                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn(?CustomerService $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn(?CustomerService $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }
}
