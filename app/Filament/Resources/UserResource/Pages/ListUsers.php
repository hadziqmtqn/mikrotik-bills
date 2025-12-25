<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Exports\UserExporter;
use App\Filament\Resources\UserResource\UserResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected static ?string $title = 'Pelanggan';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Baru')
                ->closeModalByClickingAway(false),

            ExportAction::make()
                ->label('Ekspor Pelanggan')
                ->modalHeading('Ekspor Pelanggan')
                ->exporter(UserExporter::class)
                ->stickyModalHeader()
                ->stickyModalFooter()
                ->chunkSize(100)
                ->fileDisk('s3')
        ];
    }
}
