<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Produk Dimsum';

    protected static ?string $modelLabel = 'Produk Dimsum';

    protected static ?string $pluralModelLabel = 'Produk Dimsum';

    // --- HAK AKSES ROLE ---
    
    // Semua user (Owner & Admin Cabang) boleh melihat daftar produk di sidebar
    public static function canViewAny(): bool
    {
        return true;
    }

    // Tombol 'Tambah Produk' hanya muncul untuk Owner
    public static function canCreate(): bool
    {
        return auth()->user()?->isOwner() ?? false;
    }

    // Tombol 'Edit' hanya muncul untuk Owner
    public static function canEdit($record): bool
    {
        return auth()->user()?->isOwner() ?? false;
    }

    // Tombol 'Hapus' hanya muncul untuk Owner
    public static function canDelete($record): bool
    {
        return auth()->user()?->isOwner() ?? false;
    }

    // -----------------------

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required()
                    ->label('Kategori'),

                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama Produk'),

                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->prefix('Rp')
                    ->required()
                    ->label('Harga'),

                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->directory('images')
                    ->label('Foto Produk')
                    ->columnSpanFull(),

                Forms\Components\Toggle::make('is_best_seller')
                    ->label('Favorit Pelanggan')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Foto')
                    ->circular(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nama Produk'),

                Tables\Columns\TextColumn::make('category.name')
                    ->sortable()
                    ->label('Kategori'),

                Tables\Columns\TextColumn::make('price')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->label('Harga'),

                Tables\Columns\IconColumn::make('is_best_seller')
                    ->boolean()
                    ->label('Favorit Pelanggan'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Filter Kategori'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}