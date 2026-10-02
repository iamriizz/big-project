<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockResource\Pages;
use App\Models\Branch;
use App\Models\Stock;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StockResource extends Resource
{
    protected static ?string $model = Stock::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationGroup = 'Manajemen Stok';
    protected static ?string $navigationLabel = 'Stok';

    protected static ?string $modelLabel = 'Stok';

    protected static ?string $pluralModelLabel = 'Stok';


    // Admin Cabang hanya bisa melihat stok cabangnya sendiri, Owner melihat semua.
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->whereHas('branch', fn (Builder $query) => $query->official());
        $user = auth()->user();

        if (! $user?->isOwner()) {
            $query->where('branch_id', $user?->branch_id ?? 0);
        }

        return $query;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->isOwner() ?? false;
    }

    public static function canEdit($record): bool
    {
        $user = auth()->user();

        return $user?->isOwner()
            || ($user?->isAdmin() && $record->branch_id === $user->branch_id);
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->isOwner() ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->isOwner() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('branch_id')
                    ->relationship('branch', 'name')
                    ->required()
                    ->default(fn (): ?int => auth()->user()?->branch_id)
                    ->disabled(fn (): bool => ! (auth()->user()?->isOwner() ?? false))
                    ->dehydrated()
                    ->label('Cabang'),

                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required()
                    ->disabled(fn (): bool => ! (auth()->user()?->isOwner() ?? false))
                    ->dehydrated()
                    ->label('Produk'),

                Forms\Components\TextInput::make('current_stock')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->label('Sisa Stok'),

                Forms\Components\TextInput::make('min_stock_alert')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->default(10)
                    ->disabled(fn (): bool => ! (auth()->user()?->isOwner() ?? false))
                    ->dehydrated()
                    ->label('Batas Stok Menipis'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Cabang')
                    ->searchable(),

                Tables\Columns\TextColumn::make('product.name')
                    ->label('Nama Produk')
                    ->searchable(),

                Tables\Columns\TextColumn::make('current_stock')
                    ->label('Sisa Stok')
                    ->formatStateUsing(fn (int $state): string => $state <= 0 ? 'Habis ('.$state.' pcs)' : $state.' pcs')
                    ->badge()
                    ->color(fn (Stock $record): string => $record->current_stock <= 0
                        ? 'danger'
                        : ($record->isLowStock() ? 'warning' : 'success'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('min_stock_alert')
                    ->label('Batas Minimum')
                    ->suffix(' pcs')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Diperbarui')
                    ->dateTime('d/m/Y H:i'),

                Tables\Columns\TextColumn::make('next_restock_at')
                    ->label('Jadwal Restock Berikutnya')
                    ->state(fn (Stock $record): string => $record->next_restock_at?->format('d/m/Y') ?? '-')
                    ->description(fn (Stock $record): string => match ($record->restockScheduleStatus()) {
                        'overdue' => 'Terlambat lebih dari 3 hari',
                        'today' => 'Hari ini',
                        'tomorrow' => 'H-1 pengambilan stok',
                        default => 'Dalam jadwal',
                    })
                    ->badge()
                    ->color(fn (Stock $record): string => match ($record->restockScheduleStatus()) {
                        'overdue' => 'danger',
                        'today' => 'warning',
                        'tomorrow' => 'info',
                        default => 'success',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('branch_id')
                    ->label('Cabang')
                    ->options(Branch::query()->official()->orderBy('name')->pluck('name', 'id')->all())
                    ->visible(fn (): bool => auth()->user()?->isOwner() ?? false),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListStocks::route('/'),
            'create' => Pages\CreateStock::route('/create'),
            'edit'   => Pages\EditStock::route('/{record}/edit'),
        ];
    }
}
