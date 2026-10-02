<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\Stock;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalRevenue = Transaction::sum('total_amount') ?? 0;
        $totalTransactions = Transaction::count();
        $totalProducts = Product::count();
        $lowStockCount = Stock::where('current_stock', '<=', 10)->count();

        return [
            Stat::make('Total Pendapatan', 'Rp ' . number_format($totalRevenue, 0, ',', '.'))
                ->description('Omzet Penjualan Kategori Dimsum')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary')
                ->chart([7, 10, 15, 18, 22, 30, 45]),

            Stat::make('Pesanan Masuk', $totalTransactions . ' Transaksi')
                ->description('Total transaksi pelanggan')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('primary')
                ->chart([3, 5, 8, 12, 15, 20, $totalTransactions]),

            Stat::make('Total Varian Dimsum', $totalProducts . ' Menu')
                ->description('Produk aktif di katalog')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('primary'),

            Stat::make('Peringatan Stok', $lowStockCount . ' Produk')
                ->description($lowStockCount > 0 ? 'Ada stok di bawah 10' : 'Stok semua aman')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($lowStockCount > 0 ? 'primary' : 'warning'),
        ];
    }
}