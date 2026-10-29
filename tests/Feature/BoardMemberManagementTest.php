<?php

namespace Tests\Feature;

use App\Models\BoardMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BoardMemberManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_about_page_only_displays_active_board_members_in_display_order(): void
    {
        BoardMember::create([
            'name' => 'Director Displayed First',
            'position' => 'member',
            'sort_order' => 20,
            'is_active' => true,
        ]);
        BoardMember::create([
            'name' => 'Director Hidden',
            'position' => 'member',
            'sort_order' => 21,
            'is_active' => false,
        ]);
        BoardMember::create([
            'name' => 'Director Displayed Second',
            'position' => 'chairman',
            'sort_order' => 22,
            'is_active' => true,
        ]);

        $response = $this->get(route('about'));

        $response->assertOk()
            ->assertSee('Nimal Kotawalagedara')
            ->assertSee('Director Displayed First')
            ->assertSee('Director Displayed Second')
            ->assertDontSee('Director Hidden');
        $this->assertLessThan(
            strpos($response->getContent(), 'Director Displayed Second'),
            strpos($response->getContent(), 'Director Displayed First'),
        );
    }

    public function test_public_about_page_displays_board_member_names_in_the_selected_locale(): void
    {
        BoardMember::create([
            'name' => 'English Board Director',
            'name_si' => 'සිංහල අධ්‍යක්ෂවරයා',
            'name_ta' => 'தமிழ் இயக்குநர்',
            'position' => 'member',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        foreach ([
            'en' => 'English Board Director',
            'si' => 'සිංහල අධ්‍යක්ෂවරයා',
            'ta' => 'தமிழ் இயக்குநர்',
        ] as $locale => $name) {
            $response = $this->withSession(['locale' => $locale])->get(route('about'));

            $response->assertOk()->assertSee($name);

            if ($locale !== 'en') {
                $response->assertDontSee('English Board Director');
            }
        }
    }

    public function test_board_member_management_requires_authentication(): void
    {
        $this->get(route('admin.board-members.index'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_non_admin_cannot_view_or_change_board_members(): void
    {
        $editor = User::factory()->create([
            'role' => 'editor',
            'is_approved' => true,
        ]);
        $this->actingAs($editor);

        $this->get(route('admin.activity.index'))
            ->assertOk()
            ->assertDontSee('Board of Directors');
        $this->get(route('admin.board-members.index'))->assertForbidden();
        $this->post(route('admin.board-members.store'), [
            'name' => 'Unauthorized Director',
            'position' => 'member',
            'sort_order' => 10,
        ])->assertForbidden();

        $this->assertDatabaseMissing('board_members', ['name' => 'Unauthorized Director']);
    }

    public function test_admin_can_manage_board_members_and_upload_photos(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->get(route('admin.activity.index'))
            ->assertOk()
            ->assertSee('Board of Directors');
        $this->get(route('admin.board-members.create'))
            ->assertOk()
            ->assertSee('English Name')
            ->assertSee('Sinhala Name')
            ->assertSee('Tamil Name')
            ->assertSee('type="email" name="email"', false);

        $response = $this->post(route('admin.board-members.store'), [
            'name' => 'New Board Director',
            'name_si' => 'නව අධ්‍යක්ෂවරයා',
            'name_ta' => 'புதிய இயக்குநர்',
            'position' => 'member',
            'email' => 'director@example.com',
            'sort_order' => 6,
            'image' => UploadedFile::fake()->image('director.png'),
            'is_active' => '1',
        ]);
        $boardMember = BoardMember::query()->where('name', 'New Board Director')->firstOrFail();

        $response->assertRedirect(route('admin.board-members.index'));
        $this->assertDatabaseHas('board_members', [
            'id' => $boardMember->id,
            'name' => 'New Board Director',
            'name_si' => 'නව අධ්‍යක්ෂවරයා',
            'name_ta' => 'புதிய இயக்குநர்',
            'position' => 'member',
            'email' => 'director@example.com',
            'sort_order' => 6,
            'is_active' => true,
        ]);
        Storage::disk('public')->assertExists($boardMember->image_path);
        $this->assertDatabaseHas('cms_activity_logs', [
            'subject_type' => 'board_member',
            'subject_id' => $boardMember->id,
            'action' => 'created',
        ]);
        $this->assertDatabaseHas('cms_activity_logs', [
            'subject_type' => 'board_member_photo',
            'subject_id' => $boardMember->id,
            'action' => 'uploaded',
        ]);

        $this->get(route('admin.board-members.edit', $boardMember))
            ->assertOk()
            ->assertSee('New Board Director')
            ->assertSee('නව අධ්‍යක්ෂවරයා')
            ->assertSee('புதிய இயக்குநர்');

        $previousImagePath = $boardMember->image_path;
        $this->put(route('admin.board-members.update', $boardMember), [
            'name' => 'Updated Board Director',
            'name_si' => 'යාවත්කාලීන අධ්‍යක්ෂවරයා',
            'name_ta' => 'புதுப்பிக்கப்பட்ட இயக்குநர்',
            'position' => 'chairman',
            'email' => 'updated-director@example.com',
            'sort_order' => 2,
            'image' => UploadedFile::fake()->image('updated-director.jpg'),
            'is_active' => '1',
        ])->assertRedirect(route('admin.board-members.index'));

        $boardMember->refresh();
        $this->assertSame('Updated Board Director', $boardMember->name);
        $this->assertSame('යාවත්කාලීන අධ්‍යක්ෂවරයා', $boardMember->name_si);
        $this->assertSame('புதுப்பிக்கப்பட்ட இயக்குநர்', $boardMember->name_ta);
        $this->assertSame('chairman', $boardMember->position);
        $this->assertSame('updated-director@example.com', $boardMember->email);
        $this->assertSame(2, $boardMember->sort_order);
        $this->assertTrue($boardMember->is_active);
        $this->assertNotSame($previousImagePath, $boardMember->image_path);
        Storage::disk('public')->assertMissing($previousImagePath);
        Storage::disk('public')->assertExists($boardMember->image_path);
        $this->assertDatabaseHas('cms_activity_logs', [
            'subject_type' => 'board_member',
            'subject_id' => $boardMember->id,
            'action' => 'updated',
        ]);
        $this->assertDatabaseHas('cms_activity_logs', [
            'subject_type' => 'board_member_photo',
            'subject_id' => $boardMember->id,
            'action' => 'removed',
        ]);
    }

    public function test_admin_must_provide_board_member_names_in_sinhala_and_tamil(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $response = $this->from(route('admin.board-members.create'))
            ->post(route('admin.board-members.store'), [
                'name' => 'English Board Director',
                'name_si' => '',
                'name_ta' => '',
                'position' => 'member',
                'sort_order' => 5,
            ]);

        $response->assertRedirect(route('admin.board-members.create'))
            ->assertSessionHasErrors(['name_si', 'name_ta']);
        $this->assertDatabaseMissing('board_members', ['name' => 'English Board Director']);
    }

    public function test_admin_cannot_save_invalid_board_member_email_addresses(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $formInput = [
            'name' => 'Board Director',
            'name_si' => 'අධ්‍යක්ෂක',
            'name_ta' => 'இயக்குநர்',
            'position' => 'member',
            'sort_order' => 5,
        ];

        $storeResponse = $this->from(route('admin.board-members.create'))
            ->post(route('admin.board-members.store'), array_merge($formInput, [
                'email' => 'not-an-email',
            ]));

        $storeResponse->assertRedirect(route('admin.board-members.create'))
            ->assertSessionHasErrors([
                'email' => 'The email field must be a valid email address.',
            ]);
        $this->assertDatabaseMissing('board_members', ['name' => 'Board Director']);

        $boardMember = BoardMember::create([
            'name' => 'Existing Board Director',
            'name_si' => 'දැනට සිටින අධ්‍යක්ෂක',
            'name_ta' => 'தற்போதைய இயக்குநர்',
            'position' => 'member',
            'email' => 'existing@example.com',
            'sort_order' => 6,
        ]);
        $updateResponse = $this->from(route('admin.board-members.edit', $boardMember))
            ->put(route('admin.board-members.update', $boardMember), array_merge($formInput, [
                'email' => 'missing-domain@',
            ]));

        $updateResponse->assertRedirect(route('admin.board-members.edit', $boardMember))
            ->assertSessionHasErrors([
                'email' => 'The email field must be a valid email address.',
            ]);
        $this->assertDatabaseHas('board_members', [
            'id' => $boardMember->id,
            'email' => 'existing@example.com',
        ]);
    }

    public function test_admin_can_delete_board_member_and_uploaded_photo(): void
    {
        Storage::fake('public');
        $imagePath = 'board-members/director.png';
        Storage::disk('public')->put($imagePath, 'photo');
        $boardMember = BoardMember::create([
            'name' => 'Director To Delete',
            'position' => 'member',
            'image_path' => $imagePath,
            'sort_order' => 99,
            'is_active' => true,
        ]);
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $response = $this->delete(route('admin.board-members.destroy', $boardMember));

        $response->assertRedirect(route('admin.board-members.index'));
        $this->assertDatabaseMissing('board_members', ['id' => $boardMember->id]);
        Storage::disk('public')->assertMissing($imagePath);
        $this->assertDatabaseHas('cms_activity_logs', [
            'subject_type' => 'board_member',
            'subject_id' => $boardMember->id,
            'action' => 'deleted',
        ]);
    }

    public function test_board_member_photo_must_be_an_image(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $response = $this->from(route('admin.board-members.create'))->post(route('admin.board-members.store'), [
            'name' => 'Director With Invalid Photo',
            'name_si' => 'අවලංගු ඡායාරූප සහිත අධ්‍යක්ෂක',
            'name_ta' => 'தவறான புகைப்படத்துடன் இயக்குநர்',
            'position' => 'member',
            'sort_order' => 5,
            'image' => UploadedFile::fake()->create('not-an-image.txt', 10, 'text/plain'),
        ]);

        $response->assertRedirect(route('admin.board-members.create'))
            ->assertSessionHasErrors('image');
        $this->assertDatabaseMissing('board_members', ['name' => 'Director With Invalid Photo']);
        Storage::disk('public')->assertDirectoryEmpty('board-members');
    }
}
