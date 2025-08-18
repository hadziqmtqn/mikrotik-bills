<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class Dashboard extends \Filament\Pages\Dashboard
{
    use HasFiltersForm;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    public function persistsFiltersInSession(): bool
    {
        return false;
    }

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([

                    ])
            ]);
    }
}
