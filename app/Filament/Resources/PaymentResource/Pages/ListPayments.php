<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Enums\StatusData;
use App\Filament\Resources\PaymentResource;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    protected static ?string $title = 'Pembayaran';

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua'),
            'paid' => Tab::make('Lunas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', StatusData::PAID->value)),
            'unpaid' => Tab::make('Tidak Lunas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', '!=', StatusData::PAID->value)),
        ];
    }
}
