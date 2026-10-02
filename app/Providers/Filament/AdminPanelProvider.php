<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem; // Import NavigationItem
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName('Fav Dimsum')
            ->login()
            ->colors([
                'primary' => Color::hex('#EA580C'),
                'warning' => Color::hex('#F97316'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->userMenuItems([
                // Menimpa ikon profil bawaan dari versi Filament lama. Beberapa
                // deployment masih merujuk `heroicon-o-user`, yang tidak ada
                // pada set ikon yang terpasang di server tersebut.
                'profile' => MenuItem::make()
                    ->icon('heroicon-m-user-circle'),
                MenuItem::make()
                    ->label('Pengaturan Akun')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->url('/profile'),
            ])
            // Menambahkan Tombol Kembali ke Halaman Utama / Login
            ->navigationItems([
                NavigationItem::make('Kembali ke Utama')
                    ->url('/') // Mengarahkan ke halaman utama/login
                    ->icon('heroicon-o-arrow-left-on-rectangle')
                    ->sort(-1), // Menampilkan tombol di paling atas sidebar
            ]);
    }
}
