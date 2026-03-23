<?php

namespace App\Providers\Filament;

use App\Http\Middleware\RequirePasswordChange;
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
                'primary' => Color::hex('#1a6b3c'),
                'secondary' => Color::hex('#f5a800'),
                'gray' => Color::hex('#4a5568'),
            ])

            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Documents')
                    ->icon('heroicon-o-folder-open')
                    ->collapsible(),
                NavigationGroup::make()
                    ->label('People & Access')
                    ->icon('heroicon-o-user-group')
                    ->collapsible(),
                NavigationGroup::make()
                    ->label('System')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsible(),
                NavigationGroup::make()
                    ->label('My Account')
                    ->icon('heroicon-o-user-circle')
                    ->collapsible(),
            ])

            ->sidebarCollapsibleOnDesktop()
            ->globalSearch(false)

            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn(): HtmlString => new HtmlString('
                <style>
                    /* ── User info widget ───────────────────────────────────── */
                    .hrms-user-info {
                        display: flex;
                        flex-direction: column;
                        align-items: flex-end;
                        justify-content: center;
                        padding-right: 0.5rem;
                        line-height: 1.3;
                    }
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

                    /* ── FIX: Restore icons on grouped sidebar items ─────────── */

                    /* Show the icon element that Filament hides inside groups */
                    .fi-sidebar-nav .fi-sidebar-group .fi-sidebar-item-icon,
                    nav .fi-sidebar-group li a .fi-sidebar-item-icon {
                        display: flex !important;
                        flex-shrink: 0 !important;
                        opacity: 1 !important;
                        visibility: visible !important;
                        width: 1.25rem !important;
                        height: 1.25rem !important;
                    }

                    /* Show the SVG icon inside the wrapper */
                    .fi-sidebar-group .fi-sidebar-item-icon svg {
                        display: block !important;
                        width: 1.25rem !important;
                        height: 1.25rem !important;
                        opacity: 1 !important;
                    }

                    /* Hide the bullet dot replacement */
                    .fi-sidebar-group li > a > span.fi-sidebar-item-bullet,
                    .fi-sidebar-group [class~="fi-sidebar-item-bullet"] {
                        display: none !important;
                    }

                    /* Keep consistent spacing with icon restored */
                    .fi-sidebar-group .fi-sidebar-item-label {
                        margin-left: 0.25rem;
                    }

                    /* In fully-collapsed state, keep icons centred */
                    .fi-sidebar-nav[data-collapsed] .fi-sidebar-group .fi-sidebar-item-icon,
                    .fi-sidebar[data-collapsed="true"] .fi-sidebar-group .fi-sidebar-item-icon {
                        margin-inline: auto !important;
                    }
                </style>
                ')
            )

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
                RequirePasswordChange::class,
            ]);
    }
}
