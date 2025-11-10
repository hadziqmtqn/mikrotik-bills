<?php

namespace App\Filament\Resources\InvoiceResource\Widgets;

use App\Enums\StatusData;
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
            Stat::make('Total', $this->data())
                ->description('Total Tagihan')
                ->color('success'),

            Stat::make('Total Paid', $this->data(inStatus: StatusData::PAID->value))
                ->description('Total lunas')
                ->color('info'),

            Stat::make('Total Unpiad', $this->data(notInStatus: StatusData::PAID->value))
                ->description('Total tidak lunas')
                ->color('danger'),
        ];
    }

    private function data($inStatus = null, $notInStatus = null): string
    {
        return number_format($this->getPageTableQuery()
            ->when($inStatus, fn($query) => $query->where('status', $inStatus))
            ->when($notInStatus, fn($query) => $query->where('status', '!=', $notInStatus))
            ->get()
            ->sum(function (Invoice $invoice): int {
                return $invoice->total_price ?? 0;
            }),0,',','.');
    }
}
