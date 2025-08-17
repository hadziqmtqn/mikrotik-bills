<?php

namespace App\Filament\Resources\PaymentResource\Schemas;

use App\Models\Payment;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class PaymentForm
{
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('serial_number')
                    ->required()
                    ->integer(),

                TextInput::make('code')
                    ->required(),

                TextInput::make('user_id')
                    ->required()
                    ->integer(),

                TextInput::make('invoice_id')
                    ->required()
                    ->integer(),

                TextInput::make('payment_method')
                    ->required(),

                TextInput::make('bank_account_id')
                    ->integer(),

                DatePicker::make('date'),

                TextInput::make('status')
                    ->required(),

                TextInput::make('notes'),

                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn (?Payment $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn (?Payment $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }
}
