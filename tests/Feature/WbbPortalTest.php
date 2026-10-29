<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WbbPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Welfare Benefits Board');
        $response->assertSee('Aswesuma');
        $response->assertSee('data-conferbot-widget="6ae1a94e05846cdebc02a07a"', false);
        $response->assertSee('Developed by WBB IT Team');
        $response->assertSee('Developer - Avantha Jayathunga');
    }

    public function test_conferbot_is_configured_on_every_public_page(): void
    {
        foreach ([
            route('home'),
            route('about'),
            route('schemes'),
            route('publications'),
            route('downloads'),
            route('employees'),
            route('media'),
            route('careers'),
            route('procurements'),
            route('contact.index'),
            route('search'),
        ] as $url) {
            $this->get($url)
                ->assertOk()
                ->assertSee('data-conferbot-widget="6ae1a94e05846cdebc02a07a"', false);
        }
    }

    public function test_about_page_loads_successfully(): void
    {
        $response = $this->get('/about');
        $response->assertStatus(200);
        $response->assertSee('About the Welfare Benefits Board');
    }

    public function test_schemes_page_loads_successfully(): void
    {
        $response = $this->get('/schemes');
        $response->assertStatus(200);
        $response->assertSee('Aswesuma & Special Welfare Benefit Schemes');
    }

    public function test_publications_page_loads_successfully(): void
    {
        $response = $this->get('/publications');
        $response->assertStatus(200);
    }

    public function test_downloads_page_loads_successfully(): void
    {
        $response = $this->get('/downloads');
        $response->assertStatus(200);
    }

    public function test_media_page_loads_successfully(): void
    {
        $response = $this->get('/media');
        $response->assertStatus(200);
    }

    public function test_careers_page_loads_successfully(): void
    {
        $response = $this->get('/careers');
        $response->assertStatus(200);
    }

    public function test_contact_page_loads_and_form_submits(): void
    {
        $this->fakeSuccessfulTurnstile();

        $response = $this->get('/contact');
        $response->assertStatus(200)
            ->assertSee('inputmode="numeric"', false)
            ->assertSee('pattern="0[0-9]{9}"', false)
            ->assertSee('class="cf-turnstile"', false)
            ->assertSee('data-sitekey="test-site-key"', false)
            ->assertSee('https://challenges.cloudflare.com/turnstile/v0/api.js');

        $submitResponse = $this->post('/contact/submit', [
            'name' => 'John Perera',
            'email' => 'john.perera@example.com',
            'phone' => '0712345678',
            'subject' => 'General Inquiry',
            'message' => 'This is an official inquiry regarding Aswesuma publication release dates.',
            'cf-turnstile-response' => 'valid-turnstile-token',
        ]);

        $submitResponse->assertRedirect();
        $this->assertDatabaseHas('contact_messages', [
            'email' => 'john.perera@example.com',
        ]);

        Http::assertSent(fn (ClientRequest $request): bool => $request->url() === 'https://challenges.cloudflare.com/turnstile/v0/siteverify'
            && $request['secret'] === 'test-secret-key'
            && $request['response'] === 'valid-turnstile-token'
            && $request['remoteip'] === '127.0.0.1');
    }

    public function test_contact_form_rejects_phone_numbers_that_do_not_start_with_zero_or_have_ten_digits(): void
    {
        $this->fakeSuccessfulTurnstile();

        foreach (['071234567', '07123456789', '1712345678', '07123abc89'] as $phone) {
            $response = $this->from('/contact')->post('/contact/submit', [
                'name' => 'John Perera',
                'email' => 'john.perera@example.com',
                'phone' => $phone,
                'subject' => 'General Inquiry',
                'message' => 'This is an official inquiry regarding Aswesuma publication release dates.',
                'cf-turnstile-response' => 'valid-turnstile-token',
            ]);

            $response->assertRedirect('/contact')
                ->assertSessionHasErrors([
                    'phone' => __('site.contact.phone_format_error'),
                ]);
        }

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_contact_form_rejects_missing_or_invalid_turnstile_tokens(): void
    {
        config([
            'services.turnstile.site_key' => 'test-site-key',
            'services.turnstile.secret_key' => 'test-secret-key',
        ]);
        Http::preventStrayRequests();
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => false,
                'error-codes' => ['invalid-input-response'],
            ]),
        ]);
        $submission = [
            'name' => 'John Perera',
            'email' => 'john.perera@example.com',
            'phone' => '0712345678',
            'subject' => 'General Inquiry',
            'message' => 'This is an official inquiry regarding Aswesuma publication release dates.',
        ];

        $this->from('/contact')
            ->post(route('contact.submit'), $submission)
            ->assertRedirect('/contact')
            ->assertSessionHasErrors('cf-turnstile-response');

        $this->from('/contact')
            ->post(route('contact.submit'), [...$submission, 'cf-turnstile-response' => 'invalid-token'])
            ->assertRedirect('/contact')
            ->assertSessionHasErrors('cf-turnstile-response');

        $this->assertDatabaseCount('contact_messages', 0);
        Http::assertSentCount(1);
    }

    public function test_contact_form_fails_closed_when_turnstile_is_not_configured(): void
    {
        config([
            'services.turnstile.site_key' => 'public-test-site-key',
            'services.turnstile.secret_key' => null,
        ]);
        Http::fake();

        $this->get(route('contact.index'))
            ->assertOk()
            ->assertSee(__('site.contact.captcha_unavailable'))
            ->assertDontSee('https://challenges.cloudflare.com/turnstile/v0/api.js');

        $this->post(route('contact.submit'), [
            'name' => 'John Perera',
            'email' => 'john.perera@example.com',
            'phone' => '0712345678',
            'subject' => 'General Inquiry',
            'message' => 'This is an official inquiry regarding Aswesuma publication release dates.',
            'cf-turnstile-response' => 'valid-looking-token',
        ])
            ->assertRedirect(route('contact.index'))
            ->assertSessionHasErrors('cf-turnstile-response');

        $this->assertDatabaseCount('contact_messages', 0);
        Http::assertNothingSent();
    }

    public function test_contact_form_rejects_more_than_five_submissions_per_minute(): void
    {
        $this->fakeSuccessfulTurnstile();
        $submission = [
            'name' => 'John Perera',
            'email' => 'john.perera@example.com',
            'phone' => '0712345678',
            'subject' => 'General Inquiry',
            'message' => 'This is an official inquiry regarding Aswesuma publication release dates.',
            'cf-turnstile-response' => 'valid-turnstile-token',
        ];

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.10'])
                ->post(route('contact.submit'), $submission)
                ->assertRedirect();
        }

        $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.10'])
            ->post(route('contact.submit'), $submission)
            ->assertTooManyRequests();

        $this->assertDatabaseCount('contact_messages', 5);
    }

    private function fakeSuccessfulTurnstile(): void
    {
        config([
            'services.turnstile.site_key' => 'test-site-key',
            'services.turnstile.secret_key' => 'test-secret-key',
        ]);
        Http::preventStrayRequests();
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => true,
            ]),
        ]);
    }

    public function test_admin_login_and_dashboard_access(): void
    {
        $user = User::factory()->create([
            'email' => 'testadmin@wbb.gov.lk',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $loginResponse = $this->post('/admin/login', [
            'email' => 'testadmin@wbb.gov.lk',
            'password' => 'password123',
        ]);

        $loginResponse->assertRedirect('/admin/dashboard');

        $this->actingAs($user);
        $dashboardResponse = $this->get('/admin/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('CMS Dashboard');
    }
}
