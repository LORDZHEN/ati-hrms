<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * RequirePasswordChange
 *
 * Intercepts every authenticated Filament panel request.
 * If the user still has a temporary password (must_change_password = true),
 * they are redirected to the Profile page regardless of their role.
 *
 * Exemptions (always allowed through):
 *   - The profile page itself
 *   - The logout route
 *   - Any Livewire/asset/API requests that back the profile page
 */
class RequirePasswordChange
{
    public function handle(Request $request, Closure $next): mixed
    {
        $user = Auth::user();

        // Not logged in — nothing to enforce
        if (! $user) {
            return $next($request);
        }

        // No flag set — carry on normally
        if (! $user->must_change_password) {
            return $next($request);
        }

        $profileUrl = route('filament.hrms.pages.profile');

        // ── Allow-list ───────────────────────────────────────────────────────

        // 1. Already on the profile page (exact URL match)
        if (rtrim($request->url(), '/') === rtrim($profileUrl, '/')) {
            return $next($request);
        }

        // 2. Livewire AJAX requests that power the profile page components
        //    (update-profile, change-password livewire components)
        if ($request->hasHeader('X-Livewire')) {
            return $next($request);
        }

        // 3. Logout — users must always be able to sign out
        if ($request->routeIs('filament.hrms.auth.logout')) {
            return $next($request);
        }

        // 4. Filament internal asset / chunk routes
        if ($request->routeIs('filament.asset')) {
            return $next($request);
        }

        // ── Redirect ─────────────────────────────────────────────────────────
        return redirect($profileUrl)
            ->with('status', 'Please change your temporary password before continuing.');
    }
}
