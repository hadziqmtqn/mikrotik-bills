<?php

namespace App\Filament\Resources\ApplicationResource\Pages;

use App\Filament\Resources\ApplicationResource;
use App\Models\Application;
use Filament\Resources\Pages\EditRecord;

class EditApplication extends EditRecord
{
    protected static string $resource = ApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    public function mount($record = null): void
    {
        parent::mount(Application::first()?->getRouteKey());
    }

    // Sembunyikan tombol delete (opsional)
    protected function canDelete(): bool
    {
        return false;
    }
}
