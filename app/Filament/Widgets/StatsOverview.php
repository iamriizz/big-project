<?php

namespace App\Filament\Widgets;

use App\Models\Branch;
use App\Models\Transaction;
use App\Models\User;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 2;
    use InteractsWithPageFilters;

    protected static ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        $user = auth()->user();

        // 1. TAMPILAN KHUSUS OWNER (Melihat Semua Data Cabang)
        if ($user?->isOwner()) {
            $selectedBranchId = data_get($this->filters, 'branch_id');

            // Satu query agregasi menghasilkan omzet dan jumlah transaksi tiap cabang.
            // leftJoin menjaga cabang tanpa transaksi tetap tampil dengan nilai Rp 0.
            $branchSales = Branch::query()
                ->official()
                ->select('branches.id', 'branches.name')
                ->selectRaw('COALESCE(SUM(transactions.total_amount), 0) as omzet')
                ->selectRaw('COUNT(transactions.id) as transaction_count')
                ->leftJoin('transactions', 'transactions.branch_id', '=', 'branches.id')
                ->when($selectedBranchId, fn ($query) => $query->where('branches.id', $selectedBranchId))
                ->groupBy('branches.id', 'branches.name')
                ->orderBy('branches.name')
                ->get();

            $stats = [
                Stat::make(
                    $selectedBranchId ? 'Total Omzet Cabang Terpilih' : 'Total Omzet Seluruh Cabang',
                    $this->rupiah($branchSales->sum('omzet')),
                )
                    ->description($selectedBranchId ? 'Berdasarkan filter cabang' : 'Akumulasi semua cabang')
                    ->color('primary'),

                Stat::make('Total Transaksi', $branchSales->sum('transaction_count').' Pesanan')
                    ->description('Sesuai filter cabang')
                    ->color('primary'),
            ];

            foreach ($branchSales as $branch) {
                $stats[] = Stat::make('Omzet '.$branch->name, $this->rupiah($branch->omzet))
                    ->description(number_format($branch->transaction_count, 0, ',', '.').' pesanan')
                    ->color('primary');
            }

            $stats[] = Stat::make('Total Pelanggan', User::where('role', 'user')->count().' Pelanggan')
                ->description('Akun pelanggan terdaftar')
                ->color('primary');

            return $stats;
        }

        // 2. TAMPILAN KHUSUS ADMIN CABANG (Hanya Data Cabangnya Sendiri)
        $userBranchId = $user?->branch_id;
        $query = Transaction::query()->where('branch_id', $userBranchId);

        return [
            Stat::make('Total Omzet Cabang Ini', $this->rupiah((clone $query)->sum('total_amount')))
                ->description('Penjualan di cabang kamu')
                ->color('primary'),

            Stat::make('Total Transaksi Cabang', (clone $query)->count().' Pesanan')
                ->description('Pesanan masuk di cabang kamu')
                ->color('primary'),
        ];
    }

    private function rupiah(int|float|string $amount): string
    {
        return 'Rp '.number_format((float) $amount, 0, ',', '.');
    }
}
