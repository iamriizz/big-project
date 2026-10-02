<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'Transaksi & Laporan';
    protected static ?string $navigationLabel = 'Transaksi';

    protected static ?string $modelLabel = 'Transaksi';

    protected static ?string $pluralModelLabel = 'Transaksi';


    // Menghilangkan tombol New Transaction
    public static function canCreate(): bool
    {
        return false;
    }

    // Owner can review every transaction; a branch admin can review only the
    // transactions whose branch_id matches the branch assigned to their account.
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if (! $user?->isOwner()) {
            // A missing assignment must never expose another branch's orders.
            $query->where('branch_id', $user?->branch_id ?? 0);
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Form schema
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID Nota')
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pembeli')
                    ->default('Pelanggan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Cabang')
                    ->default('-')
                    ->searchable(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total Transaksi')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Pembayaran')
                    ->badge()
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'cash', 'tunai', 'tunai (cash)' => 'success',
                        'qris' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status Pesanan')
                    ->formatStateUsing(fn (?string $state): string => Transaction::statusLabel($state))
                    ->badge()
                    ->color(fn (Transaction $record): string => $record->statusDetails()['color']),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal & Jam')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('processOrder')
                    ->label('Proses Pesanan')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->visible(fn (Transaction $record): bool => $record->status === Transaction::STATUS_PENDING)
                    ->requiresConfirmation()
                    ->modalDescription('Pesanan akan ditandai sedang disiapkan.')
                    ->action(fn (Transaction $record) => $record->update(['status' => Transaction::STATUS_PROCESSING])),

                Tables\Actions\Action::make('markReady')
                    ->label('Tandai Siap Diambil')
                    ->icon('heroicon-o-hand-raised')
                    ->color('primary')
                    ->visible(fn (Transaction $record): bool => $record->status === Transaction::STATUS_PROCESSING)
                    ->requiresConfirmation()
                    ->modalDescription('Pesanan akan ditandai siap diambil pelanggan.')
                    ->action(fn (Transaction $record) => $record->update(['status' => Transaction::STATUS_READY])),

                Tables\Actions\Action::make('completeOrder')
                    ->label('Selesaikan Pesanan')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Transaction $record): bool => $record->status === Transaction::STATUS_READY)
                    ->requiresConfirmation()
                    ->modalDescription('Pesanan akan ditandai selesai.')
                    ->action(fn (Transaction $record) => $record->update(['status' => Transaction::STATUS_COMPLETED])),

                Tables\Actions\Action::make('print')
                    ->label('Struk')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn (Transaction $record): string => route('transactions.print', $record))
                    ->openUrlInNewTab(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
        ];
    }
}
