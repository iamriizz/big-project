<?php

namespace App\Filament\Widgets;

use App\Models\Branch;
use App\Models\Stock;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class StockAlertsOverview extends BaseWidget
{
    protected static bool $isDiscovered = false;

    protected static ?int $sort = 0;

    protected static ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        $user = auth()->user();
        $branchId = $user?->isOwner() ? null : $user?->branch_id;

        $stockQuery = Stock::query()
            ->whereIn('branch_id', Branch::query()->official()->select('id'))
            ->when($branchId, fn (Builder $query) => $query->where('branch_id', $branchId))
            ->with(['branch:id,name', 'product:id,name']);

        if (! $user?->isOwner() && ! $branchId) {
            $stockQuery->whereRaw('1 = 0');
        }

        $lowStocks = (clone $stockQuery)
            ->whereColumn('current_stock', '<', 'min_stock_alert')
            ->get();

        $scheduledStocks = (clone $stockQuery)->get();
        $tomorrow = $scheduledStocks->filter(fn (Stock $stock) => $stock->restockScheduleStatus() === 'tomorrow');

        return [
            Stat::make('Stok Menipis', $lowStocks->count().' Produk')
                ->description('Membutuhkan restock secepatnya')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('primary'),

            Stat::make('H-1 Pengambilan Stok', $tomorrow->count().' Produk')
                ->description('Jadwal pengambilan stok besok')
                ->descriptionIcon('heroicon-m-clock')
                ->color('primary'),
        ];
    }
}
