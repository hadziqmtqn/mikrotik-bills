<?php

namespace App\Filament\Widgets;

use App\Enums\Months;
use App\Models\Payment;
use Filament\Support\RawJs;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class EarningChart extends ApexChartWidget
{
    protected static ?string $chartId = 'earningChart';
    protected static ?string $heading = 'Pendapatan';

    protected int | string | array $columnSpan = ['lg' => 2];

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

        $months = Months::all();

        $chartData = [];
        $categories = [];
        foreach ($months as $month) {
            $categories[] = $month->short();
            $chartData[] = $payments[$month->value] ?? 0;
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
