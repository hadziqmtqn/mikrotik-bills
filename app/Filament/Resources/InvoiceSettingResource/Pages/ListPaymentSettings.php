<?php

namespace App\Filament\Resources\InvoiceSettingResource\Pages;

use App\Filament\Resources\InvoiceSettingResource\InvoiceSettingResource;
use Filament\Resources\Pages\ListRecords;

class ListPaymentSettings extends ListRecords
{
    protected static string $resource = InvoiceSettingResource::class;

    protected static ?string $title = 'Pengaturan Faktur';

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
