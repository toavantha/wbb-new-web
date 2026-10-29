<?php

namespace Tests\Feature;

use App\Models\GovernmentAffiliation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GovernmentAffiliationManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_page_keeps_the_existing_government_affiliation_content(): void
    {
        $response = $this->get(route('about'));

        $response->assertOk()
            ->assertSee('Anura Kumara Dissanayake')
            ->assertSee('President of Sri Lanka')
            ->assertSee('Prof. Anil Jayantha Fernando')
            ->assertSee('Dr. Harshana Suriyapperuma')
            ->assertSee('Ministry of Finance, Planning and Economic Development');
        $this->assertDatabaseCount('government_affiliations', 3);
    }

    public function test_public_about_page_shows_active_government_affiliations_in_display_order_and_locale(): void
    {
        $firstAffiliation = GovernmentAffiliation::factory()->create([
            'name_en' => 'Official Displayed First',
            'name_si' => 'පළමු නිලධාරියා',
            'name_ta' => 'முதல் அதிகாரி',
            'title_en' => 'First English Title',
            'title_si' => 'පළමු සිංහල තනතුර',
            'title_ta' => 'முதல் தமிழ் பதவி',
            'ministry_en' => 'First English Ministry',
            'ministry_si' => 'පළමු සිංහල අමාත්‍යාංශය',
            'ministry_ta' => 'முதல் தமிழ் அமைச்சு',
            'sort_order' => 10,
        ]);
        GovernmentAffiliation::factory()->create([
            'name_en' => 'Hidden Official',
            'sort_order' => 11,
            'is_active' => false,
        ]);
        $secondAffiliation = GovernmentAffiliation::factory()->create([
            'name_en' => 'Official Displayed Second',
            'sort_order' => 12,
        ]);

        $this->withSession(['locale' => 'si']);
        $response = $this->get(route('about'));

        $response->assertOk()
            ->assertSee($firstAffiliation->name_si)
            ->assertSee($firstAffiliation->title_si)
            ->assertSee($firstAffiliation->ministry_si)
            ->assertSee($secondAffiliation->name_si)
            ->assertDontSee('Hidden Official');
        $this->assertLessThan(
            strpos($response->getContent(), $secondAffiliation->name_si),
            strpos($response->getContent(), $firstAffiliation->name_si),
        );
    }

    public function test_government_affiliation_management_requires_authentication(): void
    {
        $this->get(route('admin.government-affiliations.index'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_non_admin_cannot_view_or_change_government_affiliations(): void
    {
        $editor = User::factory()->create([
            'role' => 'editor',
            'is_approved' => true,
        ]);
        $this->actingAs($editor);

        $this->get(route('admin.activity.index'))
            ->assertOk()
            ->assertDontSee('Government Affiliation');
        $this->get(route('admin.government-affiliations.index'))->assertForbidden();
        $this->post(route('admin.government-affiliations.store'), $this->validAffiliationInput([
            'name_en' => 'Unauthorized Official',
        ]))->assertForbidden();

        $this->assertDatabaseMissing('government_affiliations', ['name_en' => 'Unauthorized Official']);
    }

    public function test_admin_can_create_update_and_upload_government_affiliation_photos(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->get(route('admin.activity.index'))
            ->assertOk()
            ->assertSee('Government Affiliation');
        $this->get(route('admin.government-affiliations.create'))
            ->assertOk()
            ->assertSee('English Content')
            ->assertSee('Sinhala Content')
            ->assertSee('Tamil Content');

        $response = $this->post(route('admin.government-affiliations.store'), $this->validAffiliationInput([
            'name_en' => 'New Government Official',
            'image' => UploadedFile::fake()->image('official.png'),
        ]));
        $affiliation = GovernmentAffiliation::query()->where('name_en', 'New Government Official')->firstOrFail();

        $response->assertRedirect(route('admin.government-affiliations.index'));
        $this->assertDatabaseHas('government_affiliations', [
            'id' => $affiliation->id,
            'name_en' => 'New Government Official',
            'name_si' => 'රාජ්‍ය නිලධාරියා',
            'name_ta' => 'அரச அதிகாரி',
            'title_en' => 'Official Title',
            'title_si' => 'නිල තනතුර',
            'title_ta' => 'உத்தியோகபூர்வ பதவி',
            'ministry_en' => 'Finance Ministry',
            'sort_order' => 4,
            'is_active' => 1,
        ]);
        Storage::disk('public')->assertExists($affiliation->image_path);
        $this->assertDatabaseHas('cms_activity_logs', [
            'subject_type' => 'government_affiliation',
            'subject_id' => $affiliation->id,
            'action' => 'created',
        ]);
        $this->assertDatabaseHas('cms_activity_logs', [
            'subject_type' => 'government_affiliation_photo',
            'subject_id' => $affiliation->id,
            'action' => 'uploaded',
        ]);

        $this->get(route('admin.government-affiliations.edit', $affiliation))
            ->assertOk()
            ->assertSee('New Government Official');

        $previousImagePath = $affiliation->image_path;
        $this->put(route('admin.government-affiliations.update', $affiliation), $this->validAffiliationInput([
            'name_en' => 'Updated Government Official',
            'title_en' => 'Updated Official Title',
            'sort_order' => 2,
            'image' => UploadedFile::fake()->image('updated-official.png'),
            'is_active' => '0',
        ]))->assertRedirect(route('admin.government-affiliations.index'));

        $affiliation->refresh();
        $this->assertSame('Updated Government Official', $affiliation->name_en);
        $this->assertSame('Updated Official Title', $affiliation->title_en);
        $this->assertSame(2, $affiliation->sort_order);
        $this->assertFalse($affiliation->is_active);
        $this->assertNotSame($previousImagePath, $affiliation->image_path);
        Storage::disk('public')->assertMissing($previousImagePath);
        Storage::disk('public')->assertExists($affiliation->image_path);
        $this->assertDatabaseHas('cms_activity_logs', [
            'subject_type' => 'government_affiliation',
            'subject_id' => $affiliation->id,
            'action' => 'updated',
        ]);
        $this->assertDatabaseHas('cms_activity_logs', [
            'subject_type' => 'government_affiliation_photo',
            'subject_id' => $affiliation->id,
            'action' => 'removed',
        ]);
    }

    public function test_admin_can_delete_government_affiliation_and_photo(): void
    {
        Storage::fake('public');
        $imagePath = 'government-affiliations/official.png';
        Storage::disk('public')->put($imagePath, 'photo');
        $affiliation = GovernmentAffiliation::factory()->create([
            'name_en' => 'Official To Delete',
            'image_path' => $imagePath,
        ]);
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $response = $this->delete(route('admin.government-affiliations.destroy', $affiliation));

        $response->assertRedirect(route('admin.government-affiliations.index'));
        $this->assertDatabaseMissing('government_affiliations', ['id' => $affiliation->id]);
        Storage::disk('public')->assertMissing($imagePath);
        $this->assertDatabaseHas('cms_activity_logs', [
            'subject_type' => 'government_affiliation',
            'subject_id' => $affiliation->id,
            'action' => 'deleted',
        ]);
    }

    public function test_government_affiliation_photo_must_be_an_image(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $response = $this->from(route('admin.government-affiliations.create'))
            ->post(route('admin.government-affiliations.store'), $this->validAffiliationInput([
                'name_en' => 'Official With Invalid Photo',
                'image' => UploadedFile::fake()->create('not-an-image.txt', 10, 'text/plain'),
            ]));

        $response->assertRedirect(route('admin.government-affiliations.create'))
            ->assertSessionHasErrors('image');
        $this->assertDatabaseMissing('government_affiliations', ['name_en' => 'Official With Invalid Photo']);
        Storage::disk('public')->assertDirectoryEmpty('government-affiliations');
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validAffiliationInput(array $overrides = []): array
    {
        return array_merge([
            'name_en' => 'Government Official',
            'name_si' => 'රාජ්‍ය නිලධාරියා',
            'name_ta' => 'அரச அதிகாரி',
            'title_en' => 'Official Title',
            'title_si' => 'නිල තනතුර',
            'title_ta' => 'உத்தியோகபூர்வ பதவி',
            'ministry_en' => 'Finance Ministry',
            'ministry_si' => 'මුදල් අමාත්‍යාංශය',
            'ministry_ta' => 'நிதி அமைச்சு',
            'sort_order' => 4,
            'is_active' => '1',
        ], $overrides);
    }
}
