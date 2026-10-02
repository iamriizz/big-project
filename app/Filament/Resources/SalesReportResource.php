<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalesReportResource\Pages;
use App\Models\Branch;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SalesReportResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = "heroicon-o-chart-bar";

    protected static ?string $navigationGroup = "Transaksi & Laporan";

    protected static ?string $navigationLabel = "Laporan Penjualan";

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = "Laporan Penjualan";

    protected static ?string $pluralModelLabel = "Laporan Penjualan";

    public static function canViewAny(): bool
    {
        return auth()->user()?->isOwner() ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn("branch_id", Branch::query()->official()->select("id"))
            ->with(["branch:id,name", "details.product:id,name"]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading("Laporan Penjualan")
            ->description("Ringkasan transaksi dari seluruh cabang Fav Dimsum.")
            ->columns([
                Tables\Columns\TextColumn::make("invoice_number")
                    ->label("No. Nota / Invoice")
                    ->searchable()
                    ->copyable()
                    ->weight("semibold"),

                Tables\Columns\TextColumn::make("created_at")
                    ->label("Tanggal & Jam")
                    ->dateTime("d/m/Y H:i")
                    ->sortable(),

                Tables\Columns\TextColumn::make("branch.name")
                    ->label("Cabang")
                    ->badge()
                    ->color("primary")
                    ->searchable(),

                Tables\Columns\TextColumn::make("customer_name")
                    ->label("Nama Pembeli")
                    ->default("Pelanggan")
                    ->searchable(),

                Tables\Columns\TextColumn::make("details")
                    ->label("Rincian Produk (Item & Qty)")
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
                    ->sortable()
                    ->weight("bold")
                    ->color("success"),

                Tables\Columns\TextColumn::make("status")
                    ->label("Status")
                    ->formatStateUsing(fn (?string $state): string => Transaction::statusLabel($state))
                    ->badge()
                    ->color(fn (Transaction $record): string => $record->statusDetails()["color"]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make("period")
                    ->label("Periode")
                    ->options([
                        "today" => "Hari ini",
                        "this_week" => "Minggu ini",
                        "this_month" => "Bulan ini",
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data["value"] ?? null) {
                            "today" => $query->whereDate("created_at", today()),
                            "this_week" => $query->whereBetween("created_at", [now()->startOfWeek(), now()->endOfWeek()]),
                            "this_month" => $query->whereBetween("created_at", [now()->startOfMonth(), now()->endOfMonth()]),
                            default => $query,
                        };
                    }),

                Tables\Filters\Filter::make("date_range")
                    ->label("Rentang Tanggal")
                    ->form([
                        Forms\Components\DatePicker::make("from")->label("Dari tanggal"),
                        Forms\Components\DatePicker::make("until")->label("Sampai tanggal"),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data["from"] ?? null, fn (Builder $query, $date) => $query->whereDate("created_at", ">=", $date))
                            ->when($data["until"] ?? null, fn (Builder $query, $date) => $query->whereDate("created_at", "<=", $date));
                    }),

                Tables\Filters\SelectFilter::make("branch_id")
                    ->label("Cabang")
                    ->options(Branch::query()->official()->orderBy("name")->pluck("name", "id")->all()),
            ])
            ->defaultSort("created_at", "desc");
    }

    public static function getPages(): array
    {
        return [
            "index" => Pages\ListSalesReports::route("/"),
        ];
    }
}
