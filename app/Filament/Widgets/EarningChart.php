<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class EarningChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var ?string
     */
    protected static ?string $chartId = 'earningChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'EarningChart';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $payments = Payment::selectRaw('EXTRACT(MONTH FROM "date") as month, SUM(amount) as total')
            ->whereRaw('EXTRACT(YEAR FROM "date") = ?', [date('Y')])
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->all();

        // Buat array bulan (Jan, Feb, dst)
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
}
