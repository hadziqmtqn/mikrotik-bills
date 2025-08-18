<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CustomerStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Pelanggan', $this->totalCustomer())
                ->label('')
                ->description('Total Pelanggan')
                ->color('primary')
                ->descriptionIcon($this->growthDescriptionIcon())
                ->chart($this->customerGrowthLast12Months()),

            Stat::make('Total Pelanggan Aktif', $this->totalCustomer('active'))
                ->label('')
                ->description('Pelanggan Aktif')
                ->color('info'),

            Stat::make('Pelanggan Tidak Aktif', $this->totalCustomer('not_active'))
                ->label('')
                ->description('Pelanggan Tidak Aktif')
                ->color('danger'),
        ];
    }

    private function totalCustomer($status = null): int
    {
        return User::whereHas('roles', fn($q) => $q->where('name', 'user'))
            ->when($status, fn($q) => $q->where('is_active', ($status == 'active')))
            ->count();
    }

    private function customerGrowthLast12Months(): array
    {
        $data = User::whereHas('roles', fn($q) => $q->where('name', 'user'))
            ->whereBetween('created_at', [
                now()->subMonths(11)->startOfMonth(),
                now()->endOfMonth()
            ])
            ->selectRaw('EXTRACT(YEAR FROM created_at) as year, EXTRACT(MONTH FROM created_at) as month, COUNT(*) as total')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $months = collect(range(0, 11))
            ->map(fn($i) => now()->subMonths(11 - $i)->format('Y-m'))
            ->values();

        $growth = [];
        foreach ($months as $ym) {
            $found = $data->first(fn($row) =>
                $row->year . '-' . str_pad($row->month, 2, '0', STR_PAD_LEFT) === $ym
            );
            $growth[] = (int)$found?->total;
        }

        return $growth;
    }

    private function customerGrowthLastMonth(): int
    {
        return User::whereHas('roles', fn($q) => $q->where('name', 'user'))
            ->whereBetween('created_at', [
                now()->startOfMonth(),
                now()->endOfMonth()
            ])
            ->count();
    }

    private function customerGrowthPrevMonth(): int
    {
        return User::whereHas('roles', fn($q) => $q->where('name', 'user'))
            ->whereBetween('created_at', [
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth()
            ])
            ->count();
    }

    private function growthDescriptionIcon(): string
    {
        $last = $this->customerGrowthLastMonth();
        $prev = $this->customerGrowthPrevMonth();
        if ($last > $prev) {
            return 'heroicon-m-arrow-trending-up';
        } elseif ($last < $prev) {
            return 'heroicon-m-arrow-trending-down';
        }
        return 'heroicon-m-minus-circle'; // Atau icon netral lainnya
    }
}
