<?php

namespace App\Filament\Resources\ServicePackageResource\Pages;

use App\Filament\Resources\ServicePackageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateServicePackage extends CreateRecord
{
    protected static string $resource = ServicePackageResource::class;

    protected static ?string $title = 'Buat Paket Layanan';

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
