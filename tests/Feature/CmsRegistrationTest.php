<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\CmsRegistrationAwaitingApproval;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class CmsRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_form_is_available_from_the_cms_login(): void
    {
        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee(route('admin.register'));

        $this->get(route('admin.register'))
            ->assertOk()
            ->assertSee('Verify your email address first.');
    }

    public function test_registration_creates_a_pending_editor_without_signing_them_in(): void
    {
        Notification::fake();

        $response = $this->post(route('admin.register.submit'), [
            'name' => 'New CMS User',
            'email' => 'new.editor@example.com',
            'password' => 'strong-password-123',
            'password_confirmation' => 'strong-password-123',
            'role' => 'admin',
            'is_approved' => true,
        ]);

        $response->assertRedirect(route('admin.login'));
        $response->assertSessionHas('success');
        $this->assertGuest();
        $this->assertDatabaseHas('users', [
            'name' => 'New CMS User',
            'email' => 'new.editor@example.com',
            'role' => 'editor',
            'is_approved' => false,
            'email_verified_at' => null,
        ]);
        Notification::assertSentTo(
            User::query()->where('email', 'new.editor@example.com')->firstOrFail(),
            VerifyEmail::class,
        );
    }

    public function test_verified_registration_is_added_to_approval_queue_and_notifies_approved_admins(): void
    {
        Notification::fake();
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_approved' => true,
        ]);
        $unapprovedAdmin = User::factory()->create([
            'role' => 'admin',
            'is_approved' => false,
        ]);
        $user = User::factory()->unverified()->create([
            'role' => 'editor',
            'is_approved' => false,
        ]);
        $verificationUrl = URL::temporarySignedRoute('verification.verify', now()->addMinutes(30), [
            'id' => $user->id,
            'hash' => sha1($user->getEmailForVerification()),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertDontSee($user->email);
        $this->put(route('admin.users.approve', $user))->assertNotFound();
        $this->get($verificationUrl)
            ->assertRedirect(route('admin.login'))
            ->assertSessionHas('success');

        $this->assertNotNull($user->fresh()->email_verified_at);
        $this->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee($user->email);
        $this->get($verificationUrl)
            ->assertRedirect(route('admin.login'));
        Notification::assertSentTo(
            $admin,
            CmsRegistrationAwaitingApproval::class,
            fn (CmsRegistrationAwaitingApproval $notification): bool => $notification->registeredUser->is($user),
        );
        Notification::assertNotSentTo($unapprovedAdmin, CmsRegistrationAwaitingApproval::class);
        Notification::assertCount(1);
    }

    public function test_unverified_user_cannot_sign_in_even_if_an_admin_approves_them(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'unverified.editor@example.com',
            'password' => 'strong-password-123',
            'role' => 'editor',
            'is_approved' => true,
        ]);

        $this->post(route('admin.login.submit'), [
            'email' => $user->email,
            'password' => 'strong-password-123',
        ])
            ->assertRedirect()
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_resend_verification_sends_a_link_without_disclosing_unknown_emails(): void
    {
        Notification::fake();
        $user = User::factory()->unverified()->create();

        $this->post(route('verification.send'), ['email' => $user->email])
            ->assertRedirect(route('admin.login'))
            ->assertSessionHas('success');
        $this->post(route('verification.send'), ['email' => 'unknown@example.com'])
            ->assertRedirect(route('admin.login'))
            ->assertSessionHas('success');

        Notification::assertSentTo($user, VerifyEmail::class);
        Notification::assertCount(1);
    }

    public function test_invalid_signed_verification_link_does_not_verify_the_account(): void
    {
        Notification::fake();
        $user = User::factory()->unverified()->create();
        $verificationUrl = URL::temporarySignedRoute('verification.verify', now()->addMinutes(30), [
            'id' => $user->id,
            'hash' => sha1($user->getEmailForVerification()),
        ]);
        $tamperedUrl = str_replace($user->id, $user->id + 1, $verificationUrl);

        $this->get($tamperedUrl)->assertForbidden();

        $this->assertNull($user->fresh()->email_verified_at);
        Notification::assertNothingSent();
    }

    public function test_pending_user_cannot_sign_in_until_an_admin_approves_them(): void
    {
        $user = User::factory()->create([
            'email' => 'pending.editor@example.com',
            'password' => 'strong-password-123',
            'role' => 'editor',
            'is_approved' => false,
        ]);

        $response = $this->post(route('admin.login.submit'), [
            'email' => $user->email,
            'password' => 'strong-password-123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_can_approve_pending_registration_and_user_can_sign_in(): void
    {
        $user = User::factory()->create([
            'email' => 'approved.editor@example.com',
            'password' => 'strong-password-123',
            'role' => 'editor',
            'is_approved' => false,
        ]);
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('approved.editor@example.com');

        $approvalResponse = $this->put(route('admin.users.approve', $user));

        $approvalResponse->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'is_approved' => true,
        ]);

        $this->post(route('admin.logout'));
        $loginResponse = $this->post(route('admin.login.submit'), [
            'email' => $user->email,
            'password' => 'strong-password-123',
        ]);

        $loginResponse->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->last_login_at);
    }

    public function test_admin_can_view_current_users_and_their_account_details(): void
    {
        $this->travelTo('2026-10-25 10:00:00');
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $loggedInUser = User::factory()->create([
            'name' => 'Logged In Editor',
            'email' => 'logged-in@example.com',
            'role' => 'editor',
            'is_approved' => true,
            'last_login_at' => '2026-10-24 09:15:00',
        ]);
        User::factory()->create([
            'name' => 'Never Logged In Editor',
            'email' => 'new-editor@example.com',
            'role' => 'editor',
            'is_approved' => true,
            'last_login_at' => null,
        ]);
        User::factory()->create([
            'name' => 'Pending Editor',
            'email' => 'pending@example.com',
            'role' => 'editor',
            'is_approved' => false,
        ]);

        $this->get(route('admin.users.current'))
            ->assertOk()
            ->assertSee('Logged In Editor')
            ->assertSee('logged-in@example.com')
            ->assertSee('editor')
            ->assertSee('2026-10-25 10:00')
            ->assertSee('2026-10-24 09:15')
            ->assertSee('Never logged in')
            ->assertSee('Pending Editor')
            ->assertSee('Not approved');
    }

    public function test_non_admin_cannot_view_current_users(): void
    {
        $this->actingAs(User::factory()->create([
            'role' => 'editor',
            'is_approved' => true,
        ]));

        $this->get(route('admin.users.current'))->assertForbidden();
    }

    public function test_current_user_without_a_recorded_login_time_is_not_shown_as_never_logged_in(): void
    {
        $admin = User::factory()->create([
            'name' => 'WBB Web Administrator',
            'role' => 'admin',
            'is_approved' => true,
            'last_login_at' => null,
        ]);
        $this->actingAs($admin);

        $this->get(route('admin.users.current'))
            ->assertOk()
            ->assertSee('WBB Web Administrator')
            ->assertSee('Current session; login time unavailable')
            ->assertDontSee('Never logged in');
    }

    public function test_guest_is_redirected_from_current_users_page(): void
    {
        $this->get(route('admin.users.current'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_successful_login_updates_the_last_login_timestamp(): void
    {
        $this->travelTo('2026-10-25 10:00:00');
        $user = User::factory()->create([
            'email' => 'editor@example.com',
            'password' => 'strong-password-123',
            'role' => 'editor',
            'is_approved' => true,
            'last_login_at' => null,
        ]);

        $response = $this->post(route('admin.login.submit'), [
            'email' => $user->email,
            'password' => 'strong-password-123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertSame('2026-10-25 10:00:00', $user->fresh()->last_login_at->format('Y-m-d H:i:s'));
    }

    public function test_login_rejects_more_than_five_attempts_per_minute(): void
    {
        $credentials = [
            'email' => 'admin@example.com',
            'password' => 'incorrect-password',
        ];

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('admin.login.submit'), $credentials)->assertRedirect();
        }

        $this->post(route('admin.login.submit'), $credentials)->assertTooManyRequests();
    }

    public function test_admin_can_reject_and_remove_a_pending_registration(): void
    {
        $user = User::factory()->create([
            'role' => 'editor',
            'is_approved' => false,
        ]);
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $response = $this->delete(route('admin.users.reject', $user));

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_non_admin_user_cannot_review_registrations(): void
    {
        $this->actingAs(User::factory()->create([
            'role' => 'editor',
            'is_approved' => true,
        ]));

        $this->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_registration_rejects_duplicate_email_and_unconfirmed_password(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->from(route('admin.register'))->post(route('admin.register.submit'), [
            'name' => 'New CMS User',
            'email' => 'existing@example.com',
            'password' => 'strong-password-123',
            'password_confirmation' => 'different-password-123',
        ]);

        $response->assertRedirect(route('admin.register'));
        $response->assertSessionHasErrors(['email', 'password']);
        $this->assertDatabaseCount('users', 1);
    }
}
