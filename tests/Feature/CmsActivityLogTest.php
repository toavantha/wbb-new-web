<?php

namespace Tests\Feature;

use App\Models\CmsActivityLog;
use App\Models\NewsItem;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CmsActivityLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_activity_history_requires_cms_authentication(): void
    {
        $this->get(route('admin.activity.index'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_authenticated_cms_user_can_view_activity_history(): void
    {
        $user = User::factory()->create([
            'name' => 'CMS Officer',
            'role' => 'admin',
        ]);
        $this->actingAs($user);
        CmsActivityLog::create([
            'user_id' => $user->id,
            'actor_name' => $user->name,
            'action' => 'uploaded',
            'subject_type' => 'document',
            'subject_id' => 14,
            'subject_name' => 'Annual Report',
            'details' => 'Uploaded file: annual-report.pdf',
        ]);

        $this->get(route('admin.activity.index'))
            ->assertOk()
            ->assertSee('CMS Officer')
            ->assertSee('Annual Report')
            ->assertSee('Uploaded file: annual-report.pdf')
            ->assertSee('Download log (.txt)');
    }

    public function test_activity_history_filters_logs_inclusive_of_the_selected_date_range(): void
    {
        $user = User::factory()->create(['role' => 'editor']);
        $this->actingAs($user);
        foreach ([
            ['subject_name' => 'Before range', 'created_at' => '2026-10-19 18:29:59'],
            ['subject_name' => 'Start date', 'created_at' => '2026-10-19 18:30:00'],
            ['subject_name' => 'End date', 'created_at' => '2026-10-21 18:29:59'],
            ['subject_name' => 'After range', 'created_at' => '2026-10-21 18:30:00'],
        ] as $activity) {
            $log = new CmsActivityLog([
                'user_id' => $user->id,
                'actor_name' => $user->name,
                'action' => 'updated',
                'subject_type' => 'news',
                'subject_name' => $activity['subject_name'],
            ]);
            $log->created_at = $activity['created_at'];
            $log->updated_at = $activity['created_at'];
            $log->save();
        }

        $this->get(route('admin.activity.index', [
            'start_date' => '2026-10-20',
            'end_date' => '2026-10-21',
        ]))
            ->assertOk()
            ->assertDontSee('Download log (.txt)')
            ->assertSee('Start date')
            ->assertSee('End date')
            ->assertSee('2026-10-20 00:00')
            ->assertDontSee('Before range')
            ->assertDontSee('After range');
    }

    public function test_activity_history_rejects_an_end_date_before_the_start_date(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'editor']));

        $this->get(route('admin.activity.index', [
            'start_date' => '2026-10-22',
            'end_date' => '2026-10-20',
        ]))
            ->assertRedirect()
            ->assertSessionHasErrors('end_date');
    }

    public function test_admin_can_download_activity_log_as_a_text_file_with_date_filter(): void
    {
        $admin = User::factory()->create(['name' => 'Web Admin', 'role' => 'admin']);
        $this->actingAs($admin);

        $includedLog = new CmsActivityLog([
            'user_id' => $admin->id,
            'actor_name' => $admin->name,
            'action' => 'uploaded',
            'subject_type' => 'document',
            'subject_name' => 'Included report',
            'details' => 'report.pdf',
        ]);
        $includedLog->created_at = '2026-10-20 10:30:00';
        $includedLog->updated_at = '2026-10-20 10:30:00';
        $includedLog->save();

        $excludedLog = new CmsActivityLog([
            'user_id' => $admin->id,
            'actor_name' => $admin->name,
            'action' => 'deleted',
            'subject_type' => 'news',
            'subject_name' => 'Excluded article',
        ]);
        $excludedLog->created_at = '2026-10-22 10:30:00';
        $excludedLog->updated_at = '2026-10-22 10:30:00';
        $excludedLog->save();

        $response = $this->get(route('admin.activity.export', [
            'start_date' => '2026-10-20',
            'end_date' => '2026-10-21',
        ]));

        $response->assertDownload('cms-activity-2026-10-20-to-2026-10-21.txt');
        $downloadedLog = $response->streamedContent();
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $this->assertStringContainsString('Included report', $downloadedLog);
        $this->assertStringContainsString('2026-10-20 16:00:00', $downloadedLog);
        $this->assertStringNotContainsString('Excluded article', $downloadedLog);
    }

    public function test_cms_editor_cannot_download_activity_log(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'editor']));

        $this->get(route('admin.activity.export'))->assertForbidden();
    }

    public function test_publication_list_displays_the_latest_document_uploader(): void
    {
        $user = User::factory()->create(['name' => 'Document Officer']);
        $this->actingAs($user);
        $publication = Publication::factory()->create([
            'title' => 'Annual Report',
            'file_path' => 'publications/annual-report.pdf',
        ]);
        CmsActivityLog::create([
            'user_id' => $user->id,
            'actor_name' => $user->name,
            'action' => 'uploaded',
            'subject_type' => 'document',
            'subject_id' => $publication->id,
            'subject_name' => $publication->title,
            'details' => 'Uploaded file: annual-report.pdf',
        ]);

        $this->get(route('admin.publications.index'))
            ->assertOk()
            ->assertSee('Uploaded by Document Officer');
    }

    public function test_news_and_photo_uploads_are_recorded_for_the_authenticated_user(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('admin.news.store'), [
            'title' => 'Community outreach programme',
            'category' => 'Announcement',
            'content' => 'The Board held an outreach programme.',
            'published_date' => '2026-10-23',
            'main_image' => UploadedFile::fake()->image('main.png'),
            'sub_images' => [UploadedFile::fake()->image('gallery.png')],
        ]);

        $newsItem = NewsItem::query()->where('title', 'Community outreach programme')->firstOrFail();
        $response->assertRedirect(route('admin.news.index'));
        $this->assertDatabaseHas('cms_activity_logs', [
            'user_id' => $user->id,
            'actor_name' => $user->name,
            'action' => 'created',
            'subject_type' => 'news',
            'subject_id' => $newsItem->id,
            'subject_name' => $newsItem->title,
        ]);
        $this->assertDatabaseHas('cms_activity_logs', [
            'user_id' => $user->id,
            'action' => 'uploaded',
            'subject_type' => 'news_photo',
            'subject_id' => $newsItem->id,
            'details' => 'Uploaded main photo: main.png',
        ]);
        $this->assertDatabaseHas('cms_activity_logs', [
            'user_id' => $user->id,
            'action' => 'uploaded',
            'subject_type' => 'news_photo',
            'subject_id' => $newsItem->id,
            'details' => 'Uploaded gallery photo: gallery.png',
        ]);
        $this->get(route('admin.news.index'))
            ->assertOk()
            ->assertSee('Content by '.$user->name)
            ->assertSee('Photos by '.$user->name);
    }

    public function test_publication_document_upload_is_recorded_for_the_authenticated_user(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('admin.publications.store'), [
            'title' => 'Annual Report 2026',
            'category' => 'Circular',
            'document_number' => 'WBB/2026/01',
            'language' => 'English',
            'description' => 'Annual report.',
            'published_date' => '2026-10-23',
            'is_active' => '1',
            'file' => UploadedFile::fake()->create('annual-report.pdf', 100, 'application/pdf'),
        ]);

        $publication = Publication::query()->where('title', 'Annual Report 2026')->firstOrFail();
        $response->assertRedirect(route('admin.publications.index'));
        $this->assertDatabaseHas('cms_activity_logs', [
            'user_id' => $user->id,
            'action' => 'uploaded',
            'subject_type' => 'document',
            'subject_id' => $publication->id,
            'subject_name' => 'Annual Report 2026',
            'details' => 'Uploaded file: annual-report.pdf',
        ]);
    }

    public function test_downloadable_document_upload_is_recorded_for_the_authenticated_user(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('admin.downloads.store'), [
            'title' => 'Appeals Form',
            'category' => 'Application Form',
            'document_number' => 'WBB/APP/2026/01',
            'language' => 'English',
            'description' => 'Application form.',
            'published_date' => '2026-10-23',
            'is_active' => '1',
            'file' => UploadedFile::fake()->create('appeals-form.pdf', 100, 'application/pdf'),
        ]);

        $download = Publication::query()->where('title', 'Appeals Form')->firstOrFail();
        $response->assertRedirect(route('admin.downloads.index'));
        $this->assertDatabaseHas('cms_activity_logs', [
            'user_id' => $user->id,
            'action' => 'uploaded',
            'subject_type' => 'document',
            'subject_id' => $download->id,
            'subject_name' => 'Appeals Form',
            'details' => 'Uploaded file: appeals-form.pdf',
        ]);
    }

    public function test_vacancy_document_upload_is_recorded_for_the_authenticated_user(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('admin.careers.store'), [
            'title' => 'Document processing officer',
            'department' => 'Operations',
            'employment_type' => 'Full-Time',
            'location' => 'Head Office',
            'description' => 'Process official documents.',
            'qualifications' => 'Relevant experience.',
            'closing_date' => '2026-12-31',
            'tor_en_file' => UploadedFile::fake()->create('terms.pdf', 100, 'application/pdf'),
        ]);

        $response->assertRedirect(route('admin.careers.index'));
        $this->assertDatabaseHas('cms_activity_logs', [
            'user_id' => $user->id,
            'action' => 'uploaded',
            'subject_type' => 'career_document',
            'subject_name' => 'Document processing officer',
            'details' => 'Uploaded tor: terms.pdf',
        ]);
    }

    public function test_employee_photo_upload_is_recorded_for_the_authenticated_user(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('admin.employees.store'), [
            'name' => 'Nadeesha Perera',
            'name_si' => 'නදීෂා පෙරේරා',
            'name_ta' => 'நதீஷா பெரேரா',
            'designation' => 'Programme Officer',
            'designation_si' => 'වැඩසටහන් නිලධාරිනී',
            'designation_ta' => 'நிகழ்ச்சித் திட்ட அலுவலர்',
            'email' => 'nadeesha@example.com',
            'image' => UploadedFile::fake()->image('employee.png'),
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.employees.index'));
        $this->assertDatabaseHas('cms_activity_logs', [
            'user_id' => $user->id,
            'action' => 'uploaded',
            'subject_type' => 'employee_photo',
            'subject_name' => 'Nadeesha Perera',
            'details' => 'Uploaded employee photo: employee.png',
        ]);
        $this->get(route('admin.employees.index'))
            ->assertOk()
            ->assertSee('Photo by '.$user->name);
    }
}
