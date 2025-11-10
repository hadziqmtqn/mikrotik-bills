<?php

namespace App\Filament\Clusters\Reference\Resources\ExtraCostResource\Pages;

use App\Filament\Clusters\Reference\Resources\ExtraCostResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\MaxWidth;

class ListExtraCosts extends ListRecords
{
    protected static string $resource = ExtraCostResource::class;

    protected static ?string $title = 'Biaya Tambahan';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Baru')
                ->modalHeading('Tambah Biaya Tambahan')
                ->modalWidth(MaxWidth::Medium),
        ];
    }
}
