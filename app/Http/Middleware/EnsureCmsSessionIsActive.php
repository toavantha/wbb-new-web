<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCmsSessionIsActive
{
    public const LAST_ACTIVITY_SESSION_KEY = 'cms_last_activity_at';

    private const IDLE_TIMEOUT_MINUTES = 10;

    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()->is_approved) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')
                ->with('success', 'Your CMS account is no longer approved. Please contact an administrator.');
        }

        if (
            $request->user()->must_change_password
            && ! $request->routeIs('admin.password.edit', 'admin.password.update', 'admin.logout')
        ) {
            return redirect()->route('admin.password.edit')
                ->with('error', 'Your administrator set a temporary password. Change it before continuing.');
        }

        $lastActivityAt = $request->session()->get(self::LAST_ACTIVITY_SESSION_KEY);

        if (is_int($lastActivityAt) && $lastActivityAt <= now()->subMinutes(self::IDLE_TIMEOUT_MINUTES)->timestamp) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')
                ->with('success', 'Your CMS session expired due to inactivity. Please sign in again.');
        }

        $request->session()->put(self::LAST_ACTIVITY_SESSION_KEY, now()->timestamp);

        return $next($request);
    }
}
