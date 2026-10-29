<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Cookie;
use Tests\TestCase;

class CmsSessionTimeoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_cms_session_expires_after_ten_minutes_without_activity(): void
    {
        $this->travelTo('2026-10-25 10:10:00');
        $user = User::factory()->create();
        $this->actingAs($user)->withSession([
            'cms_last_activity_at' => now()->subMinutes(10)->timestamp,
        ]);

        $response = $this->get(route('admin.password.edit'));

        $response->assertRedirect(route('admin.login'));
        $response->assertSessionHas('success', 'Your CMS session expired due to inactivity. Please sign in again.');
        $this->assertGuest();
    }

    public function test_cms_activity_refreshes_the_ten_minute_idle_timeout(): void
    {
        $this->travelTo('2026-10-25 10:09:00');
        $user = User::factory()->create();
        $this->actingAs($user)->withSession([
            'cms_last_activity_at' => now()->subMinutes(9)->timestamp,
        ]);

        $this->get(route('admin.password.edit'))
            ->assertOk()
            ->assertSessionHas('cms_last_activity_at', now()->timestamp);

        $this->assertAuthenticatedAs($user);
    }

    public function test_cms_login_uses_a_browser_session_cookie_without_remembering_the_user(): void
    {
        $user = User::factory()->create([
            'email' => 'cms.user@example.com',
            'password' => 'strong-password-123',
            'remember_token' => null,
        ]);

        $response = $this->post(route('admin.login.submit'), [
            'email' => $user->email,
            'password' => 'strong-password-123',
            'remember' => true,
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertNull($user->fresh()->remember_token);
        $response->assertSessionHas('cms_last_activity_at');

        $sessionCookie = collect($response->headers->getCookies())
            ->first(fn (Cookie $cookie): bool => $cookie->getName() === config('session.cookie'));

        $this->assertNotNull($sessionCookie);
        $this->assertSame(0, $sessionCookie->getExpiresTime());
    }
}
