<?php

namespace App\Filament\Resources\InvoiceSettingResource\Pages;

use App\Filament\Resources\InvoiceSettingResource\InvoiceSettingResource;
use Filament\Resources\Pages\EditRecord;

class EditPaymentSetting extends EditRecord
{
    protected static string $resource = InvoiceSettingResource::class;

    protected static ?string $title = 'Ubah Pengaturan Faktur';

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
