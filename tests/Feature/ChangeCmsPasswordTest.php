<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ChangeCmsPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_open_password_settings(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get(route('admin.password.edit'))
            ->assertOk()
            ->assertSee('Current Password')
            ->assertSee('Confirm New Password');
    }

    public function test_guest_is_redirected_from_password_settings(): void
    {
        $this->get(route('admin.password.edit'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_authenticated_user_can_change_their_password_with_the_current_password(): void
    {
        $user = User::factory()->create(['password' => 'current-password-123']);
        $this->actingAs($user);

        $response = $this->put(route('admin.password.update'), [
            'current_password' => 'current-password-123',
            'password' => 'new-password-456',
            'password_confirmation' => 'new-password-456',
        ]);

        $response->assertRedirect(route('admin.password.edit'));
        $response->assertSessionHas('success');
        $this->assertAuthenticatedAs($user);
        $this->assertTrue(Hash::check('new-password-456', $user->fresh()->password));
    }

    public function test_user_can_change_an_admin_set_temporary_password_and_continue_in_the_cms(): void
    {
        $user = User::factory()->create([
            'password' => 'temporary-password-123',
            'must_change_password' => true,
        ]);
        $this->actingAs($user);

        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.password.edit'));

        $response = $this->put(route('admin.password.update'), [
            'current_password' => 'temporary-password-123',
            'password' => 'permanent-password-456',
            'password_confirmation' => 'permanent-password-456',
        ]);

        $response->assertRedirect(route('admin.password.edit'));
        $this->assertTrue(Hash::check('permanent-password-456', $user->fresh()->password));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'must_change_password' => false,
        ]);

        $this->get(route('admin.dashboard'))->assertOk();
    }

    public function test_password_change_is_rejected_when_current_password_is_incorrect(): void
    {
        $user = User::factory()->create(['password' => 'current-password-123']);
        $this->actingAs($user);

        $response = $this->from(route('admin.password.edit'))->put(route('admin.password.update'), [
            'current_password' => 'incorrect-password',
            'password' => 'new-password-456',
            'password_confirmation' => 'new-password-456',
        ]);

        $response->assertRedirect(route('admin.password.edit'));
        $response->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('current-password-123', $user->fresh()->password));
    }

    public function test_password_change_requires_confirmation_and_a_different_new_password(): void
    {
        $user = User::factory()->create(['password' => 'current-password-123']);
        $this->actingAs($user);

        $response = $this->from(route('admin.password.edit'))->put(route('admin.password.update'), [
            'current_password' => 'current-password-123',
            'password' => 'current-password-123',
            'password_confirmation' => 'different-password-456',
        ]);

        $response->assertRedirect(route('admin.password.edit'));
        $response->assertSessionHasErrors('password');
        $this->assertTrue(Hash::check('current-password-123', $user->fresh()->password));
    }
}
