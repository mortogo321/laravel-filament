<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\ChartWidget;

class ProductChart extends ChartWidget
{
    protected function getData(): array
    {
        $categories = Product::selectRaw('category, count(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Products per category',
                    'data' => array_values($categories),
                    'backgroundColor' => [
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)',
                        'rgb(251, 146, 60)',
                        'rgb(244, 63, 94)',
                        'rgb(168, 85, 247)',
                    ],
                ],
            ],
            'labels' => array_keys($categories),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    public function getHeading(): ?string
    {
        return 'Products by Category';
    }
}
