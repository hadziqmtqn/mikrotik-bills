<?php

namespace App\Filament\Resources\InvoiceResource\Schemas;

use App\Models\Invoice;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

class InvoiceForm
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

                DatePicker::make('date'),

                DatePicker::make('due_date'),

                DatePicker::make('cancel_date'),

                TextInput::make('status')
                    ->required(),

                TextInput::make('note'),

                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn(?Invoice $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn(?Invoice $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }
}
