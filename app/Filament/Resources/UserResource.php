<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Manajemen Pengguna';
    protected static ?string $navigationLabel = 'Pengguna';

    protected static ?string $modelLabel = 'Pengguna';

    protected static ?string $pluralModelLabel = 'Pengguna';


    // Hanya Owner yang bisa melihat menu Kelola User di Sidebar
    public static function canViewAny(): bool
    {
        return auth()->user()?->isOwner() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama Lengkap'),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->label('Alamat Email'),

                Forms\Components\Select::make('role')
                    ->options([
                        'owner' => 'Pemilik',
                        'admin' => 'Admin Cabang',
                        'user'  => 'Pelanggan',
                    ])
                    ->required()
                    ->live() // Update form secara realtime saat role dipilih
                    ->label('Peran Akses'),

                Forms\Components\Select::make('branch_id')
                    ->relationship('branch', 'name')
                    ->label('Tugaskan ke Cabang')
                    ->placeholder('Pilih Cabang')
                    // Hanya muncul jika role yang dipilih adalah 'admin'
                    ->visible(fn (Get $get) => $get('role') === 'admin')
                    ->required(fn (Get $get) => $get('role') === 'admin'),

                Forms\Components\TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->label('Kata Sandi'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Nama'),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->label('Alamat Email'),

                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'owner' => 'danger',
                        'admin' => 'warning',
                        'user'  => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'owner' => 'Pemilik',
                        'admin' => 'Admin Cabang',
                        'user' => 'Pelanggan',
                        default => $state,
                    })
                    ->label('Peran'),

                Tables\Columns\TextColumn::make('branch.name')
                    ->default('-')
                    ->label('Cabang Tugas'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}