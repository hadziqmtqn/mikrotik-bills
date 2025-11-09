<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Filament\Resources\AdminResource;
use App\Filament\Resources\ApplicationResource;
use App\Filament\Resources\BankAccountResource;
use App\Filament\Resources\CustomerServiceResource;
use App\Filament\Resources\InvoiceResource;
use App\Filament\Resources\InvoiceSettingResource;
use App\Filament\Resources\PaymentResource;
use App\Filament\Resources\RouterResource;
use App\Filament\Resources\ServicePackageResource;
use App\Filament\Resources\UserResource;
use App\Filament\Widgets\CustomerStatsOverview;
use App\Filament\Widgets\EarningChart;
use App\Models\Application;
use BezhanSalleh\FilamentShield\Resources\RoleResource;
use Exception;
use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Livewire\Notifications;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Alignment;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;

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

        Notifications::alignment(Alignment::Center);

        return $panel
            ->default()
            ->id('admin')
            ->path('/')
            ->login()
            ->profile()
            ->passwordReset()
            ->topNavigation($application?->navigation_position == 'top')
            ->brandName($application?->short_name ?? config('app.name'))
            ->favicon($application?->favicon)
            ->colors([
                'primary' => $colorMap[$application?->panel_color ?? 'amber'] ?? Color::Amber,
            ])
            ->font('Poppins')
            ->collapsibleNavigationGroups(false)
            ->breadcrumbs(false)
            ->sidebarCollapsibleOnDesktop()
            ->sidebarFullyCollapsibleOnDesktop(false)
            ->unsavedChangesAlerts(false)
            ->databaseTransactions()
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            //->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                CustomerStatsOverview::class,
                EarningChart::class
            ])
            ->navigationGroups([
                'Main',
                'Service',
                'Payment',
                'Invoice',
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
                FilamentApexChartsPlugin::make()
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->navigation(function (NavigationBuilder $navigationBuilder): NavigationBuilder {
                return $navigationBuilder
                    ->items([
                        ...Dashboard::getNavigationItems(),
                        ...$this->filterResourceNavigationItems(UserResource::class)
                    ])
                    ->groups([
                        NavigationGroup::make('Service')
                            ->items([
                                ...$this->filterResourceNavigationItems(ServicePackageResource::class),
                                ...$this->filterResourceNavigationItems(CustomerServiceResource::class),
                            ]),
                        NavigationGroup::make('Payment')
                            ->items([
                                ...$this->filterResourceNavigationItems(PaymentResource::class),
                                ...$this->filterResourceNavigationItems(BankAccountResource::class),
                            ]),
                        NavigationGroup::make('Invoice')
                            ->items([
                                ...$this->filterResourceNavigationItems(InvoiceSettingResource::class),
                                ...$this->filterResourceNavigationItems(InvoiceResource::class),
                            ]),
                        NavigationGroup::make('Network')
                            ->items([
                                ...$this->filterResourceNavigationItems(RouterResource::class),
                            ]),
                        NavigationGroup::make('System')
                            ->items([
                                ...$this->filterResourceNavigationItems(RoleResource::class),
                                ...$this->filterResourceNavigationItems(AdminResource::class),
                                ...$this->filterResourceNavigationItems(ApplicationResource::class),
                            ]),
                    ]);
            })
            ->spa()
            ->spaUrlExceptions([
                '*/users/*'
            ]);
    }

    function filterResourceNavigationItems($resource) {
        // Buang namespace model menjadi dan pisahkan dengan ::, misal model InvoicePayment menjadi invoice::payment
        $permission = 'view_any_' . str_replace('_', '::', Str::snake(class_basename($resource::getModel())));

        if (Gate::allows($permission)) {
            return $resource::getNavigationItems();
        }

        return [];
    }
}
