<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class Dashboard extends \Filament\Pages\Dashboard
{
    use HasFiltersForm;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    public function persistsFiltersInSession(): bool
    {
        return false;
    }

    public function getColumns(): int|string|array
    {
        return 4;
    }
}
