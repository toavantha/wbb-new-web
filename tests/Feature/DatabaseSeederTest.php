<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_configured_initial_admin_is_email_verified_when_seeded(): void
    {
        config([
            'cms.initial_admin.name' => 'Initial Administrator',
            'cms.initial_admin.email' => 'initial.admin@example.com',
            'cms.initial_admin.password' => 'secure-password-123',
        ]);

        app(DatabaseSeeder::class)->run();

        $admin = User::query()->where('email', 'initial.admin@example.com')->firstOrFail();

        $this->assertNotNull($admin->email_verified_at);
        $this->assertTrue($admin->is_approved);
    }

    public function test_seeding_verifies_an_existing_unverified_initial_admin(): void
    {
        $admin = User::factory()->unverified()->create([
            'email' => 'initial.admin@example.com',
            'role' => 'editor',
            'is_approved' => false,
        ]);
        config([
            'cms.initial_admin.name' => 'Initial Administrator',
            'cms.initial_admin.email' => 'initial.admin@example.com',
            'cms.initial_admin.password' => 'secure-password-123',
        ]);

        app(DatabaseSeeder::class)->run();

        $admin->refresh();

        $this->assertNotNull($admin->email_verified_at);
        $this->assertSame('admin', $admin->role);
        $this->assertTrue($admin->is_approved);
    }
}
