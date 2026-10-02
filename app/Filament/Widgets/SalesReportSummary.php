<?php

namespace App\Filament\Widgets;

use App\Models\Branch;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class SalesReportSummary extends BaseWidget
{
    protected static bool $isDiscovered = false;

    protected int | string | array $columnSpan = "full";

    /** @var array<string, mixed> */
    public array $tableFilters = [];

    protected function getStats(): array
    {
        $query = Transaction::query()
            ->whereIn("branch_id", Branch::query()->official()->select("id"));

        $period = data_get($this->tableFilters, "period.value");
        $branchId = data_get($this->tableFilters, "branch_id.value");
        $from = data_get($this->tableFilters, "date_range.from");
        $until = data_get($this->tableFilters, "date_range.until");

        match ($period) {
            "today" => $query->whereDate("created_at", today()),
            "this_week" => $query->whereBetween("created_at", [now()->startOfWeek(), now()->endOfWeek()]),
            "this_month" => $query->whereBetween("created_at", [now()->startOfMonth(), now()->endOfMonth()]),
            default => null,
        };

        $query
            ->when($branchId, fn (Builder $query) => $query->where("branch_id", $branchId))
            ->when($from, fn (Builder $query) => $query->whereDate("created_at", ">=", $from))
            ->when($until, fn (Builder $query) => $query->whereDate("created_at", "<=", $until));

        return [
            Stat::make("Total Pemasukan", "Rp ".number_format((float) (clone $query)->sum("total_amount"), 0, ",", "."))
                ->description("Sesuai filter laporan")
                ->icon("heroicon-m-banknotes")
                ->color("primary"),

            Stat::make("Total Transaksi", (clone $query)->count()." Pesanan")
                ->description("Sesuai filter laporan")
                ->icon("heroicon-m-shopping-bag")
                ->color("primary"),
        ];
    }
}
