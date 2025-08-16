<?php

namespace App\Filament\Resources\PaymentSettingResource\Pages;

use App\Filament\Resources\InvoiceSettingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPaymentSettings extends ListRecords
{
    protected static string $resource = InvoiceSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //CreateAction::make(),
        ];
    }
}
