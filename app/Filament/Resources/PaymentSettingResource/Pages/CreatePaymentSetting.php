<?php

namespace App\Filament\Resources\PaymentSettingResource\Pages;

use App\Filament\Resources\InvoiceSettingResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePaymentSetting extends CreateRecord
{
    protected static string $resource = InvoiceSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
