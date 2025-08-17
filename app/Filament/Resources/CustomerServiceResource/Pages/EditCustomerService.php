<?php

namespace App\Filament\Resources\CustomerServiceResource\Pages;

use App\Filament\Resources\CustomerServiceResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditCustomerService extends EditRecord
{
    protected static string $resource = CustomerServiceResource::class;
    protected static ?string $title = 'Edit';

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
