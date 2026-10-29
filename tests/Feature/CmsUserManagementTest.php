<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CmsUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_edit_and_update_a_cms_user_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create([
            'name' => 'CMS Editor',
            'email' => 'editor@example.com',
            'role' => 'editor',
            'is_approved' => true,
        ]);
        $this->actingAs($admin);

        $this->get(route('admin.users.edit', $user))
            ->assertOk()
            ->assertSee('CMS Editor')
            ->assertSee('editor@example.com');

        $response = $this->put(route('admin.users.update', $user), [
            'name' => 'Updated CMS Editor',
            'email' => 'updated.editor@example.com',
            'role' => 'admin',
            'is_approved' => '0',
        ]);

        $response->assertRedirect(route('admin.users.current'));
        $response->assertSessionHas('success', 'CMS user details updated.');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated CMS Editor',
            'email' => 'updated.editor@example.com',
            'role' => 'admin',
            'is_approved' => false,
        ]);
    }

    public function test_admin_can_delete_another_user_account(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $user = User::factory()->create(['role' => 'editor']);

        $response = $this->delete(route('admin.users.destroy', $user));

        $response->assertRedirect(route('admin.users.current'));
        $response->assertSessionHas('success', 'CMS user deleted.');
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_admin_can_set_a_temporary_password_that_must_be_changed(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $user = User::factory()->create([
            'password' => 'old-password-123',
            'must_change_password' => false,
        ]);

        $response = $this->post(route('admin.users.temporary-password', $user), [
            'temporary_password' => 'temporary-password-456',
            'temporary_password_confirmation' => 'temporary-password-456',
        ]);

        $response->assertRedirect(route('admin.users.edit', $user));
        $response->assertSessionHas('success');
        $this->assertTrue(Hash::check('temporary-password-456', $user->fresh()->password));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'must_change_password' => true,
        ]);
    }

    public function test_temporary_password_form_rejects_unconfirmed_or_short_passwords(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $user = User::factory()->create(['password' => 'old-password-123']);

        $response = $this->from(route('admin.users.edit', $user))->post(route('admin.users.temporary-password', $user), [
            'temporary_password' => 'short',
            'temporary_password_confirmation' => 'different',
        ]);

        $response->assertRedirect(route('admin.users.edit', $user));
        $response->assertSessionHasErrors('temporary_password');
        $this->assertTrue(Hash::check('old-password-123', $user->fresh()->password));
        $this->assertFalse($user->fresh()->must_change_password);
    }

    public function test_admin_cannot_delete_their_own_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $this->delete(route('admin.users.destroy', $admin))->assertForbidden();

        $this->assertModelExists($admin);
    }

    public function test_admin_cannot_remove_the_last_approved_administrators_access(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_approved' => true,
        ]);
        $editor = User::factory()->create([
            'role' => 'editor',
            'is_approved' => true,
        ]);
        $this->actingAs($admin);

        $this->put(route('admin.users.update', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => 'editor',
            'is_approved' => '1',
        ])->assertForbidden();

        $this->delete(route('admin.users.destroy', $admin))->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'role' => 'admin',
            'is_approved' => true,
        ]);
        $this->assertModelExists($editor);
    }

    public function test_admin_cannot_assign_an_email_already_used_by_another_account(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $user = User::factory()->create(['email' => 'editor@example.com']);
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->from(route('admin.users.edit', $user))->put(route('admin.users.update', $user), [
            'name' => $user->name,
            'email' => 'existing@example.com',
            'role' => 'editor',
            'is_approved' => '1',
        ]);

        $response->assertRedirect(route('admin.users.edit', $user));
        $response->assertSessionHasErrors('email');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'editor@example.com',
        ]);
    }

    public function test_non_admin_cannot_manage_cms_user_accounts(): void
    {
        $user = User::factory()->create(['role' => 'editor']);
        $target = User::factory()->create(['role' => 'editor']);
        $this->actingAs($user);

        $this->get(route('admin.users.edit', $target))->assertForbidden();
        $this->put(route('admin.users.update', $target), [
            'name' => 'Changed Name',
            'email' => $target->email,
            'role' => 'editor',
            'is_approved' => '1',
        ])->assertForbidden();
        $this->delete(route('admin.users.destroy', $target))->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'name' => $target->name,
        ]);
    }

    public function test_user_without_approval_is_logged_out_on_their_next_cms_request(): void
    {
        $user = User::factory()->create([
            'role' => 'editor',
            'is_approved' => false,
        ]);
        $this->actingAs($user);

        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('admin.login'));
        $response->assertSessionHas('success', 'Your CMS account is no longer approved. Please contact an administrator.');
        $this->assertGuest();
    }
}
