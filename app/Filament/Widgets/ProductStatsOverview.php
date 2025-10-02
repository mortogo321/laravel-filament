<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProductStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalProducts = Product::count();
        $activeProducts = Product::where('status', 'active')->count();
        $totalValue = Product::sum('price');
        $lowStock = Product::where('stock', '<', 20)->count();
        $featuredProducts = Product::where('is_featured', true)->count();

        return [
            Stat::make('Total Products', $totalProducts)
                ->description('All products in inventory')
                ->descriptionIcon('heroicon-o-shopping-bag')
                ->color('primary')
                ->chart([7, 12, 15, 18, 22, 25, $totalProducts]),

            Stat::make('Active Products', $activeProducts)
                ->description(($activeProducts / max($totalProducts, 1) * 100) . '% of total')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Total Value', '$' . number_format($totalValue, 2))
                ->description('Inventory value')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('warning'),

            Stat::make('Low Stock', $lowStock)
                ->description('Products below 20 units')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color($lowStock > 0 ? 'danger' : 'success'),

            Stat::make('Featured Products', $featuredProducts)
                ->description('Highlighted in store')
                ->descriptionIcon('heroicon-o-star')
                ->color('info'),
        ];
    }
}
