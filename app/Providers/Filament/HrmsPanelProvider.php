<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Navigation\NavigationGroup;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class HrmsPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->databaseNotifications()
            ->id('hrms')
            ->path('hrms')
            ->authGuard('web')
            ->login(\App\Filament\Pages\Auth\Login::class)
            ->registration(\App\Filament\Pages\Auth\Register::class)
            ->favicon(asset('images/Main_Logo-removebg-preview.png'))
            ->colors([
                'primary' => Color::hex('#1a6b3c'),   // ATI deep forest green
                'secondary' => Color::hex('#f5a800'), // ATI institutional gold
                'gray' => Color::hex('#4a5568'),
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('My Account')
                    ->collapsible(),
            ])
            ->sidebarCollapsibleOnDesktop()
            ->globalSearch(false)

            // ── Inject CSS for user info dark/light mode ──
            // ── Inject CSS for user info dark/light mode ──
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn(): HtmlString => new HtmlString('
                <style>
                    .hrms-user-info {
                        display: flex;
                        flex-direction: column;
                        align-items: flex-end;
                        justify-content: center;
                        padding-right: 0.5rem;
                        line-height: 1.3;
                    }

                    /* Force override everything Filament sets */
                    span.hrms-user-name,
                    div.hrms-user-info span.hrms-user-name {
                        font-size: 0.875rem !important;
                        font-weight: 700 !important;
                        white-space: nowrap !important;
                        color: #111827 !important;
                        opacity: 1 !important;
                        text-shadow: none !important;
                        -webkit-text-fill-color: #111827 !important;
                    }

                    span.hrms-user-role,
                    div.hrms-user-info span.hrms-user-role {
                        font-size: 0.6875rem !important;
                        font-weight: 600 !important;
                        white-space: nowrap !important;
                        text-transform: uppercase !important;
                        letter-spacing: 0.07em !important;
                        color: #16a34a !important;
                        opacity: 1 !important;
                        -webkit-text-fill-color: #16a34a !important;
                    }

                    html.dark span.hrms-user-name,
                    html.dark div.hrms-user-info span.hrms-user-name {
                        color: #f1f5f9 !important;
                        -webkit-text-fill-color: #f1f5f9 !important;
                    }

                    html.dark span.hrms-user-role,
                    html.dark div.hrms-user-info span.hrms-user-role {
                        color: #4ade80 !important;
                        -webkit-text-fill-color: #4ade80 !important;
                    }
                </style>
            ')
            )

            // ── User name stacked over role, displayed beside the avatar ──
            ->renderHook(
                PanelsRenderHook::USER_MENU_BEFORE,
                fn(): HtmlString => new HtmlString(
                    Auth::check()
                    ? '<div class="hrms-user-info">
               <span class="hrms-user-name" style="color:#111827;-webkit-text-fill-color:#111827;font-weight:700;font-size:0.875rem;opacity:1;">'
                    . e(Auth::user()->full_name ?: Auth::user()->name)
                    . '</span>
               <span class="hrms-user-role" style="color:#16a34a;-webkit-text-fill-color:#16a34a;font-weight:600;font-size:0.6875rem;text-transform:uppercase;opacity:1;">'
                    . e(Auth::user()->getRoleDisplayName())
                    . '</span>
           </div>'
                    : ''
                )
            )
            // ─────────────────────────────────────────────────────────────

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                \App\Filament\Widgets\NotificationBell::class,
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
            ]);
    }
}
