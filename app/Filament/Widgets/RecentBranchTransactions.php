<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentBranchTransactions extends BaseWidget
{
    protected static ?int $sort = 4;

    protected static ?string $pollingInterval = "15s";

    protected int | string | array $columnSpan = "full";

    protected static ?string $heading = "Laporan Penjualan & Transaksi Terbaru";

    public static function canView(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function table(Table $table): Table
    {
        $branchId = auth()->user()?->branch_id;

        return $table
            ->query(
                Transaction::query()
                    ->where("branch_id", $branchId ?? 0)
                    ->with(["details.product:id,name"])
                    ->orderBy("created_at", "desc")
            )
            ->columns([
                Tables\Columns\TextColumn::make("invoice_number")
                    ->label("No. Nota")
                    ->searchable()
                    ->weight("semibold"),

                Tables\Columns\TextColumn::make("created_at")
                    ->label("Jam Pesan")
                    ->dateTime("d/m/Y H:i"),

                Tables\Columns\TextColumn::make("customer_name")
                    ->label("Nama Pembeli")
                    ->default("Pelanggan")
                    ->searchable(),

                Tables\Columns\TextColumn::make("details")
                    ->label("Rincian Menu")
                    ->state(fn (Transaction $record): string => $record->details
                        ->map(function ($detail): string {
                            $quantity = $detail->quantity ?: $detail->qty;

                            return ($detail->product?->name ?? "Produk")." × ".$quantity;
                        })
                        ->join(", "))
                    ->wrap(),

                Tables\Columns\TextColumn::make("total_amount")
                    ->label("Total Pembayaran")
                    ->money("IDR")
                    ->weight("bold")
                    ->color("primary"),

                Tables\Columns\TextColumn::make("status")
                    ->label("Status")
                    ->formatStateUsing(fn (?string $state): string => Transaction::statusLabel($state))
                    ->badge()
                    ->color(fn (Transaction $record): string => $record->statusDetails()["color"]),
            ])
            ->paginated([8, 10])
            ->defaultPaginationPageOption(8);
    }
}
