<?php

namespace App\Filament\Clusters\Reference\Resources\ExtraCostResource\Pages;

use App\Filament\Clusters\Reference\Resources\ExtraCostResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExtraCost extends EditRecord
{
    protected static string $resource = ExtraCostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
