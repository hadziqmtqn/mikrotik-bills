<?php

namespace App\Filament\Resources\InvoiceResource\Widgets;

use App\Filament\Resources\InvoiceResource\Pages\ListInvoices;
use App\Models\Invoice;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InvoiceOverview extends BaseWidget
{
    use InteractsWithPageTable;
    protected static ?string $pollingInterval = null;
    public array $tableColumnSearches = [];

    protected function getTablePage(): string
    {
        return ListInvoices::class;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Total', number_format($this->getPageTableQuery()->get()->sum(fn(Invoice $invoice) => $invoice->total_price),0,',','.'))
                ->description('Total Tagihan')
                ->color('success'),

            Stat::make('Total Paid', number_format($this->getPageTableQuery()->where('status', 'paid')->get()->sum(fn(Invoice $invoice) => $invoice->total_price),0,',','.'))
                ->description('Total lunas')
                ->color('info'),

            Stat::make('Total Unpiad', number_format($this->getPageTableQuery()->where('status', '!=', 'paid')->get()->sum(fn(Invoice $invoice) => $invoice->total_price),0,',','.'))
                ->description('Total tidak lunas')
                ->color('danger'),
        ];
    }
}
