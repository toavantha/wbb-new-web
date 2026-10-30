<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceHttpsAndSecurityHeaders
{
    private const CONTENT_SECURITY_POLICY = "default-src 'self'; base-uri 'self'; object-src 'none'; frame-ancestors 'self'; form-action 'self'; script-src 'self' https://unpkg.com https://cdn.conferbot.com https://challenges.cloudflare.com https://maps.googleapis.com; style-src 'self' https://fonts.googleapis.com https://maps.googleapis.com; font-src 'self' https://fonts.gstatic.com data:; img-src 'self' data: blob: https://wbb.gov.lk https://www.wbb.gov.lk https://maps.googleapis.com https://*.google.com https://*.gstatic.com https://challenges.cloudflare.com; connect-src 'self' https://challenges.cloudflare.com https://maps.googleapis.com https://*.googleapis.com https://*.gstatic.com https://*.conferbot.com wss://*.conferbot.com; frame-src 'self' https://challenges.cloudflare.com https://*.google.com https://*.openstreetmap.org https://*.conferbot.com; worker-src 'self' blob:; upgrade-insecure-requests";

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isProduction = app()->isProduction();

        // 🚀 Cloud-Safe Verification: Honors secure OpenShift proxy router parameters
        $isReallySecure = $request->isSecure() || $request->header('X-Forwarded-Proto') === 'https';

        if ($isProduction && !$isReallySecure) {
            // Fall back gracefully to the actual request host if config('app.url') resolves poorly
            $baseUrl = config('app.url');
            if (empty($baseUrl) || str_contains($baseUrl, 'localhost')) {
                $baseUrl = 'https://' . $request->getHttpHost();
            }

            $secureBaseUrl = preg_replace('/^http:/i', 'https:', rtrim((string) $baseUrl, '/'));
            $securePath = '/'.ltrim($request->getPathInfo(), '/');
            $queryString = $request->getQueryString();

            if ($queryString !== null) {
                $securePath .= '?'.$queryString;
            }

            return redirect()->to($secureBaseUrl.$securePath, 301);
        }

        $response = $next($request);

        if (! app()->isLocal()) {
            $response->headers->set('Content-Security-Policy', self::CONTENT_SECURITY_POLICY);
        }
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        if ($isProduction && $isReallySecure) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
