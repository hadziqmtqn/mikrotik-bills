<?php

namespace App\Filament\Clusters\Reference\Resources\BankAccountResource\Pages;

use App\Filament\Clusters\Reference\Resources\BankAccountResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBankAccount extends CreateRecord
{
    protected static string $resource = BankAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
