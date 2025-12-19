<?php

namespace App\Filament\Resources\CustomerServiceResource\Pages;

use App\Filament\Resources\CustomerServiceResource\CustomerServiceResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditCustomerService extends EditRecord
{
    protected static string $resource = CustomerServiceResource::class;
    protected static ?string $title = 'Edit Layanan';

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->modalHeading('Hapus layanan'),
            ForceDeleteAction::make()->modalHeading('Hapus selamanya'),
            RestoreAction::make()->modalHeading('Pulihkan data'),
        ];
    }
}
