<?php

namespace App\Filament\Resources\InvoiceResource\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Form;

class InvoiceForm
{
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                DateTimePicker::make('due_date')
                    ->native(false)
                    ->inlineLabel()
                    ->columnSpanFull(),
            ]);
    }
}
