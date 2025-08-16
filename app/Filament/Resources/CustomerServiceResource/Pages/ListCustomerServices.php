<?php

namespace App\Filament\Resources\CustomerServiceResource\Pages;

use App\Filament\Resources\CustomerServiceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCustomerServices extends ListRecords
{
    protected static string $resource = CustomerServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
