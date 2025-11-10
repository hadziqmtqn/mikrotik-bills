<?php

namespace App\Filament\Clusters\Reference\Resources\BankAccountResource\Pages;

use App\Filament\Clusters\Reference\Resources\BankAccountResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBankAccounts extends ListRecords
{
    protected static string $resource = BankAccountResource::class;

    protected static ?string $title = 'Rekening Bank';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Baru')
                ->modalHeading('Tambah Rekening Bank'),
        ];
    }
}
