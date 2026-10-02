<?php

namespace App\Filament\Widgets;

use App\Models\Branch;
use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;

class MonthlySalesTrendChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected static ?string $pollingInterval = '15s';

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Tren Penjualan Bulanan per Cabang';

    protected static ?string $description = 'Perbandingan omzet setiap cabang pada tahun berjalan';

    public static function canView(): bool
    {
        return auth()->user()?->isOwner() ?? false;
    }

    public function updatedFilters(): void
    {
        // ChartWidget mencache data selama lifecycle Livewire.
        $this->cachedData = null;
    }

    protected function getData(): array
    {
        $year = now()->year;
        $selectedBranchId = data_get($this->filters, 'branch_id');
        $monthExpression = DB::connection()->getDriverName() === 'sqlite'
            ? "CAST(strftime('%m', created_at) AS INTEGER)"
            : 'MONTH(created_at)';

        // Satu query agregat untuk seluruh cabang dan bulan, bukan query per kurva.
        $sales = Transaction::query()
            ->select('branch_id')
            ->selectRaw("{$monthExpression} as month_number")
            ->selectRaw('SUM(total_amount) as total_sales')
            ->whereIn('branch_id', Branch::query()->official()->select('id'))
            ->whereYear('created_at', $year)
            ->when($selectedBranchId, fn ($query) => $query->where('branch_id', $selectedBranchId))
            ->groupBy('branch_id')
            ->groupByRaw($monthExpression)
            ->get()
            ->groupBy('branch_id');

        $branches = Branch::query()
            ->official()
            ->when($selectedBranchId, fn ($query) => $query->whereKey($selectedBranchId))
            ->orderBy('name')
            ->get(['id', 'name']);

        $colors = ['#dc2626', '#2563eb', '#16a34a', '#9333ea', '#ea580c', '#0891b2', '#db2777'];

        $datasets = $branches->values()->map(function (Branch $branch, int $index) use ($sales, $colors): array {
            $monthlySales = $sales->get($branch->id, collect())->keyBy('month_number');

            return [
                'label' => $branch->name,
                'data' => collect(range(1, 12))
                    ->map(fn (int $month) => (float) data_get($monthlySales->get($month), 'total_sales', 0))
                    ->all(),
                'borderColor' => $colors[$index % count($colors)],
                'backgroundColor' => $colors[$index % count($colors)],
                'fill' => false,
                'tension' => 0.35,
                'pointRadius' => 3,
                'pointHoverRadius' => 6,
            ];
        })->all();

        return [
            'datasets' => $datasets,
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'locale' => 'id-ID',
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'format' => [
                            'style' => 'currency',
                            'currency' => 'IDR',
                            'minimumFractionDigits' => 0,
                        ],
                    ],
                ],
            ],
        ];
    }
}
