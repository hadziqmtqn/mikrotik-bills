<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Support\RawJs;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class EarningChart extends ApexChartWidget
{
    protected static ?string $chartId = 'earningChart';
    protected static ?string $heading = 'Pendapatan';

    protected function getFilters(): ?array
    {
        $fiveYearsAgo = now()->subYears(5)->year;
        $years = collect(range($fiveYearsAgo, now()->year))->reverse()->values();

        // Kembalikan associative array: [2025 => 2025, 2024 => 2024, ...]
        return $years->mapWithKeys(fn ($year) => [$year => $year])->toArray();
    }

    protected function getOptions(): array
    {
        $year = $this->filter ?? now()->year;

        $payments = Payment::selectRaw('EXTRACT(MONTH FROM "date") as month, SUM(amount) as total')
            ->whereRaw('EXTRACT(YEAR FROM "date") = ?', [$year])
            ->where('status', 'paid')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->all();

        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun',
            7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
        ];

        $chartData = [];
        $categories = [];
        foreach ($months as $num => $name) {
            $categories[] = $name;
            $chartData[] = $payments[$num] ?? 0;
        }

        return [
            'chart' => [
                'type' => 'line',
                'height' => 400,
                'toolbar' => [
                    'show' => false,
                ]
            ],
            'series' => [
                [
                    'name' => 'Earning',
                    'data' => $chartData,
                ],
            ],
            'xaxis' => [
                'categories' => $categories,
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['#f59e0b'],
            'stroke' => [
                'curve' => 'smooth',
            ],
        ];
    }

    protected function extraJsOptions(): ?RawJs
    {
        return RawJs::make(<<<'JS'
        {
            yaxis: {
                labels: {
                    formatter: function(val) {
                        return 'Rp ' + val.toLocaleString('id-ID');
                    }
                }
            }
        }
        JS);
    }
}