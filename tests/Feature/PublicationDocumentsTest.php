<?php

namespace Tests\Feature;

use App\Models\Publication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class PublicationDocumentsTest extends TestCase
{
    use RefreshDatabase;

    private ?string $fakeDiskRoot = null;

    protected function tearDown(): void
    {
        if ($this->fakeDiskRoot !== null) {
            File::deleteDirectory($this->fakeDiskRoot);
        }

        parent::tearDown();
    }

    public function test_authenticated_admin_can_upload_pdf_publication(): void
    {
        $this->fakePublicStorage();
        $this->actingAs(User::factory()->create());

        $response = $this->post(route('admin.publications.store'), [
            ...$this->publicationAttributes(),
            'file' => UploadedFile::fake()->create('gazette-2026.pdf', 250, 'application/pdf'),
        ]);

        $response->assertRedirect(route('admin.publications.index'))
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success', 'Publication added successfully.');

        $publication = Publication::query()->where('title', 'Welfare Benefits Gazette 2026')->firstOrFail();

        $this->assertNotNull($publication->file_path);
        $this->assertEquals('250.0 KB', $publication->file_size);
        $this->assertDatabaseHas('publications', [
            'id' => $publication->id,
            'title' => 'Welfare Benefits Gazette 2026',
            'category' => 'Gazette',
            'file_path' => $publication->file_path,
        ]);

        Storage::disk('public')->assertExists($publication->file_path);
    }

    public function test_authenticated_admin_can_upload_docx_publication(): void
    {
        $this->fakePublicStorage();
        $this->actingAs(User::factory()->create());

        $response = $this->post(route('admin.publications.store'), [
            ...$this->publicationAttributes([
                'title' => 'Circular Guidelines 2026',
                'category' => 'Circular',
            ]),
            'file' => UploadedFile::fake()->create('circular.docx', 500, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
        ]);

        $response->assertRedirect(route('admin.publications.index'))
            ->assertSessionHasNoErrors();

        $publication = Publication::query()->where('title', 'Circular Guidelines 2026')->firstOrFail();

        $this->assertNotNull($publication->file_path);
        $this->assertEquals('500.0 KB', $publication->file_size);
        Storage::disk('public')->assertExists($publication->file_path);
    }

    public function test_file_is_required_when_creating_publication(): void
    {
        $this->fakePublicStorage();
        $this->actingAs(User::factory()->create());

        $response = $this->from(route('admin.publications.create'))
            ->post(route('admin.publications.store'), $this->publicationAttributes());

        $response->assertRedirect(route('admin.publications.create'))
            ->assertSessionHasErrors(['file']);

        $this->assertDatabaseCount('publications', 0);
    }

    public function test_publication_upload_rejects_unsupported_file_types(): void
    {
        $this->fakePublicStorage();
        $this->actingAs(User::factory()->create());

        $response = $this->from(route('admin.publications.create'))
            ->post(route('admin.publications.store'), [
                ...$this->publicationAttributes(),
                'file' => UploadedFile::fake()->create('document.txt', 10, 'text/plain'),
            ]);

        $response->assertRedirect(route('admin.publications.create'))
            ->assertSessionHasErrors(['file']);

        $this->assertDatabaseCount('publications', 0);
    }

    public function test_authenticated_admin_can_update_publication_with_new_file(): void
    {
        $this->fakePublicStorage();
        $this->actingAs(User::factory()->create());

        $initialPath = UploadedFile::fake()->create('initial.pdf', 100, 'application/pdf')
            ->store('publications', 'public');

        $publication = Publication::factory()->create([
            'title' => 'Initial Title',
            'file_path' => $initialPath,
            'file_size' => '100.0 KB',
        ]);

        Storage::disk('public')->assertExists($initialPath);

        $response = $this->put(route('admin.publications.update', $publication->id), [
            ...$this->publicationAttributes([
                'title' => 'Updated Title',
            ]),
            'file' => UploadedFile::fake()->create('replacement.pdf', 300, 'application/pdf'),
        ]);

        $response->assertRedirect(route('admin.publications.index'))
            ->assertSessionHasNoErrors();

        $publication->refresh();
        $this->assertEquals('Updated Title', $publication->title);
        $this->assertNotEquals($initialPath, $publication->file_path);
        $this->assertEquals('300.0 KB', $publication->file_size);

        Storage::disk('public')->assertMissing($initialPath);
        Storage::disk('public')->assertExists($publication->file_path);
    }

    public function test_authenticated_admin_can_update_publication_without_reuploading_file(): void
    {
        $this->fakePublicStorage();
        $this->actingAs(User::factory()->create());

        $filePath = UploadedFile::fake()->create('existing.pdf', 100, 'application/pdf')
            ->store('publications', 'public');

        $publication = Publication::factory()->create([
            'title' => 'Original Title',
            'file_path' => $filePath,
            'file_size' => '100.0 KB',
        ]);

        $response = $this->put(route('admin.publications.update', $publication->id), [
            ...$this->publicationAttributes([
                'title' => 'Changed Title Only',
            ]),
        ]);

        $response->assertRedirect(route('admin.publications.index'))
            ->assertSessionHasNoErrors();

        $publication->refresh();
        $this->assertEquals('Changed Title Only', $publication->title);
        $this->assertEquals($filePath, $publication->file_path);
        Storage::disk('public')->assertExists($filePath);
    }

    public function test_deleting_publication_removes_file_from_storage(): void
    {
        $this->fakePublicStorage();
        $this->actingAs(User::factory()->create());

        $filePath = UploadedFile::fake()->create('to-delete.pdf', 100, 'application/pdf')
            ->store('publications', 'public');

        $publication = Publication::factory()->create([
            'file_path' => $filePath,
        ]);

        Storage::disk('public')->assertExists($filePath);

        $response = $this->delete(route('admin.publications.destroy', $publication->id));

        $response->assertRedirect(route('admin.publications.index'))
            ->assertSessionHas('success', 'Publication deleted successfully.');

        $this->assertDatabaseMissing('publications', ['id' => $publication->id]);
        Storage::disk('public')->assertMissing($filePath);
    }

    public function test_public_user_can_view_active_pdf_publication_inline(): void
    {
        $this->fakePublicStorage();

        $filePath = UploadedFile::fake()->create('public-doc.pdf', 100, 'application/pdf')
            ->store('publications', 'public');

        $publication = Publication::factory()->create([
            'title' => 'Annual Report 2026',
            'document_number' => null,
            'file_path' => $filePath,
            'is_active' => true,
        ]);

        $response = $this->get(route('publications.download', $publication->id));
        $response->assertOk()
            ->assertHeader('Content-Disposition', 'inline; filename="annual-report-2026.pdf"');
    }

    public function test_public_user_can_download_active_publication_as_an_attachment(): void
    {
        $this->fakePublicStorage();

        $filePath = UploadedFile::fake()->create('public-doc.pdf', 100, 'application/pdf')
            ->store('publications', 'public');

        $publication = Publication::factory()->create([
            'title' => 'Annual Report 2026',
            'document_number' => null,
            'file_path' => $filePath,
            'is_active' => true,
        ]);

        $response = $this->get(route('publications.download', [
            'publication' => $publication->id,
            'download' => 1,
        ]));

        $response->assertOk()
            ->assertHeader('Content-Disposition', 'attachment; filename=annual-report-2026.pdf');
    }

    public function test_publication_search_treats_like_wildcards_as_literal_characters(): void
    {
        Publication::factory()->create(['title' => 'Circular 100% complete']);
        Publication::factory()->create(['title' => 'Circular 100 percent complete']);
        Publication::factory()->create(['title' => 'Bid_Award_Notice']);
        Publication::factory()->create(['title' => 'BidXAwardXNotice']);

        $this->get(route('publications', ['search' => '%']))
            ->assertOk()
            ->assertSee('Circular 100% complete')
            ->assertDontSee('Circular 100 percent complete');

        $this->get(route('publications', ['search' => '_']))
            ->assertOk()
            ->assertSee('Bid_Award_Notice')
            ->assertDontSee('BidXAwardXNotice');
    }

    public function test_legacy_publication_url_redirects_only_to_an_allowlisted_https_host(): void
    {
        $publication = Publication::factory()->create([
            'file_path' => null,
            'file_url' => 'http://wbb.gov.lk/backend/assets/publications/legacy.pdf',
            'is_active' => true,
        ]);

        $this->get(route('publications.download', $publication))
            ->assertRedirect('https://wbb.gov.lk/backend/assets/publications/legacy.pdf');
    }

    public function test_publication_download_rejects_untrusted_or_non_http_urls(): void
    {
        foreach ([
            'https://attacker.example/phishing',
            'https://wbb.gov.lk.attacker.example/phishing',
            'javascript:alert(1)',
            '//attacker.example/phishing',
            'https://user@wbb.gov.lk/private',
            'https://[invalid-host',
        ] as $fileUrl) {
            $publication = Publication::factory()->create([
                'file_path' => null,
                'file_url' => $fileUrl,
                'is_active' => true,
            ]);

            $this->get(route('publications.download', $publication))
                ->assertNotFound();
        }
    }

    public function test_publications_page_offers_view_and_download_for_pdf_documents(): void
    {
        $this->fakePublicStorage();

        $filePath = UploadedFile::fake()->create('public-doc.pdf', 100, 'application/pdf')
            ->store('publications', 'public');

        $publication = Publication::factory()->create([
            'title' => 'Annual Report 2026',
            'file_path' => $filePath,
            'is_active' => true,
        ]);

        $response = $this->get(route('publications'));

        $response->assertOk()
            ->assertSee(route('publications.download', $publication->id), false)
            ->assertSee(route('publications.download', [
                'publication' => $publication->id,
                'download' => 1,
            ]), false)
            ->assertSee(__('site.publications.view'))
            ->assertSee(__('site.publications.download'));
    }

    public function test_publications_page_offers_view_for_circular_docx_documents(): void
    {
        $this->fakePublicStorage();

        $filePath = UploadedFile::fake()->create('circular.docx', 100, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')
            ->store('publications', 'public');

        $publication = Publication::factory()->create([
            'title' => 'Circular Guidelines 2026',
            'category' => 'Circular',
            'file_path' => $filePath,
            'is_active' => true,
        ]);

        $response = $this->get(route('publications'));

        $response->assertOk()
            ->assertSee(route('publications.download', [
                'publication' => $publication->id,
                'view' => 1,
            ]), false)
            ->assertSee(route('publications.download', [
                'publication' => $publication->id,
                'download' => 1,
            ]), false);
    }

    public function test_public_user_can_open_circular_docx_with_inline_content_disposition(): void
    {
        $this->fakePublicStorage();

        $filePath = UploadedFile::fake()->create('circular.docx', 100, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')
            ->store('publications', 'public');

        $publication = Publication::factory()->create([
            'title' => 'Circular Guidelines 2026',
            'category' => 'Circular',
            'document_number' => null,
            'file_path' => $filePath,
            'is_active' => true,
        ]);

        $response = $this->get(route('publications.download', [
            'publication' => $publication->id,
            'view' => 1,
        ]));

        $response->assertOk()
            ->assertHeader('Content-Disposition', 'inline; filename="circular-guidelines-2026.docx"');
    }

    public function test_publications_page_offers_view_for_acts_and_gazettes(): void
    {
        $this->fakePublicStorage();

        $filePath = UploadedFile::fake()->create('gazette-act.pdf', 100, 'application/pdf')
            ->store('publications', 'public');

        $gazette = Publication::factory()->create([
            'title' => 'Official Gazette 2026',
            'category' => 'Gazette',
            'file_path' => $filePath,
            'is_active' => true,
        ]);

        $response = $this->get(route('publications'));

        $response->assertOk()
            ->assertSee(route('publications.download', $gazette->id), false)
            ->assertSee(route('publications.download', [
                'publication' => $gazette->id,
                'download' => 1,
            ]), false);
    }

    public function test_home_page_offers_view_and_download_for_circulars_and_gazettes(): void
    {
        $this->fakePublicStorage();

        $filePath = UploadedFile::fake()->create('gazette-doc.pdf', 100, 'application/pdf')
            ->store('publications', 'public');

        $pub = Publication::factory()->create([
            'title' => 'Welfare Gazette Extraordinary',
            'category' => 'Gazette',
            'file_path' => $filePath,
            'published_date' => now()->toDateString(),
            'is_active' => true,
        ]);

        $response = $this->get(route('home'));

        $response->assertOk()
            ->assertSee(route('publications.download', $pub->id), false)
            ->assertSee(route('publications.download', [
                'publication' => $pub->id,
                'download' => 1,
            ]), false);
    }

    public function test_inactive_publication_cannot_be_downloaded_by_guest(): void
    {
        $this->fakePublicStorage();

        $filePath = UploadedFile::fake()->create('secret.pdf', 100, 'application/pdf')
            ->store('publications', 'public');

        $publication = Publication::factory()->create([
            'file_path' => $filePath,
            'is_active' => false,
        ]);

        $response = $this->get(route('publications.download', $publication->id));
        $response->assertNotFound();
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function publicationAttributes(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Welfare Benefits Gazette 2026',
            'category' => 'Gazette',
            'document_number' => 'WBB/GAZ/2026/01',
            'language' => 'English',
            'description' => 'Official gazette notification.',
            'published_date' => '2026-01-15',
            'is_active' => true,
        ], $overrides);
    }

    private function fakePublicStorage(): void
    {
        $diskName = 'publication-documents-'.Str::uuid();
        $this->fakeDiskRoot = storage_path('framework/testing/disks/'.$diskName);

        Storage::set('public', Storage::fake($diskName));
    }
}
