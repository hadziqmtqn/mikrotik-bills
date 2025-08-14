<?php

namespace App\Providers\Filament;

use App\Models\Application;
use Exception;
use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    /**
     * @throws Exception
     */
    public function panel(Panel $panel): Panel
    {
        $application = null;
        if (Schema::hasTable('applications')) {
            $application = Application::first();
        }

        $colorMap = [
            'slate'    => Color::Slate,
            'gray'     => Color::Gray,
            'zinc'     => Color::Zinc,
            'neutral'  => Color::Neutral,
            'stone'    => Color::Stone,
            'red'      => Color::Red,
            'orange'   => Color::Orange,
            'amber'    => Color::Amber,
            'yellow'   => Color::Yellow,
            'lime'     => Color::Lime,
            'green'    => Color::Green,
            'emerald'  => Color::Emerald,
            'teal'     => Color::Teal,
            'cyan'     => Color::Cyan,
            'sky'      => Color::Sky,
            'blue'     => Color::Blue,
            'indigo'   => Color::Indigo,
            'violet'   => Color::Violet,
            'purple'   => Color::Purple,
            'fuchsia'  => Color::Fuchsia,
            'pink'     => Color::Pink,
            'rose'     => Color::Rose,
        ];

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile()
            ->passwordReset()
            ->topNavigation($application?->navigation_position == 'top')
            ->brandName($application?->short_name ?? config('app.name'))
            ->favicon($application?->favicon)
            ->colors([
                'primary' => $colorMap[$application?->panel_color ?? 'amber'] ?? Color::Amber,
            ])
            ->collapsibleNavigationGroups(false)
            ->sidebarCollapsibleOnDesktop()
            ->sidebarFullyCollapsibleOnDesktop(false)
            ->unsavedChangesAlerts(false)
            ->databaseTransactions()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            //->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                /*Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,*/
            ])
            ->navigationGroups([
                'Main',
                'Network',
                'System'
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
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
