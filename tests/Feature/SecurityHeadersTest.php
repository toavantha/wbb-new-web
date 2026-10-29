<?php

namespace Tests\Feature;

use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    public function test_application_responses_include_security_headers(): void
    {
        $this->get('/up')
            ->assertOk()
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Content-Security-Policy')
            ->assertHeaderMissing('Strict-Transport-Security');
    }

    public function test_production_requests_over_http_redirect_to_https(): void
    {
        $this->app->detectEnvironment(fn (): string => 'production');
        config(['app.url' => 'http://wbb.example.test']);

        $this->get('/up?source=test')
            ->assertMovedPermanently()
            ->assertRedirect('https://wbb.example.test/up?source=test');
    }

    public function test_https_redirect_uses_the_configured_host_instead_of_the_request_host(): void
    {
        $this->app->detectEnvironment(fn (): string => 'production');
        config(['app.url' => 'http://wbb.example.test']);

        $this->withServerVariables(['HTTP_HOST' => 'attacker.example'])
            ->get('/up')
            ->assertMovedPermanently()
            ->assertRedirect('https://wbb.example.test/up');
    }

    public function test_production_https_responses_include_hsts(): void
    {
        $this->app->detectEnvironment(fn (): string => 'production');
        config(['app.url' => 'https://wbb.example.test']);

        $response = $this->get('https://localhost:8000/up');

        $response
            ->assertOk()
            ->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains')
            ->assertHeaderContains(
                'Content-Security-Policy',
                "img-src 'self' data: blob: https://wbb.gov.lk https://www.wbb.gov.lk https://maps.googleapis.com https://*.google.com https://*.gstatic.com https://challenges.cloudflare.com"
            );

        $this->assertStringNotContainsString(
            "'unsafe-inline'",
            $response->headers->get('Content-Security-Policy')
        );
        $this->assertStringNotContainsString(
            'img-src \'self\' data: blob: https: http:',
            $response->headers->get('Content-Security-Policy')
        );
    }
}
