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

class DownloadDocumentsTest extends TestCase
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

    public function test_authenticated_admin_can_upload_pdf_form_or_handbook(): void
    {
        $this->fakePublicStorage();
        $this->actingAs(User::factory()->create());

        $response = $this->post(route('admin.downloads.store'), [
            ...$this->downloadAttributes(),
            'file' => UploadedFile::fake()->create('aswesuma-form.pdf', 350, 'application/pdf'),
        ]);

        $response->assertRedirect(route('admin.downloads.index'))
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success', 'Downloadable item created successfully.');

        $download = Publication::query()->where('title', 'Aswesuma Phase II Appeals Form')->firstOrFail();

        $this->assertNotNull($download->file_path);
        $this->assertEquals('350.0 KB', $download->file_size);
        $this->assertEquals('Application Form', $download->category);

        Storage::disk('public')->assertExists($download->file_path);
    }

    public function test_authenticated_admin_can_upload_docx_guideline(): void
    {
        $this->fakePublicStorage();
        $this->actingAs(User::factory()->create());

        $response = $this->post(route('admin.downloads.store'), [
            ...$this->downloadAttributes([
                'title' => 'Beneficiary Handbook 2026',
                'category' => 'Handbook',
            ]),
            'file' => UploadedFile::fake()->create('handbook.docx', 800, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
        ]);

        $response->assertRedirect(route('admin.downloads.index'))
            ->assertSessionHasNoErrors();

        $download = Publication::query()->where('title', 'Beneficiary Handbook 2026')->firstOrFail();

        $this->assertNotNull($download->file_path);
        $this->assertEquals('800.0 KB', $download->file_size);
        Storage::disk('public')->assertExists($download->file_path);
    }

    public function test_file_is_required_when_creating_download_item(): void
    {
        $this->fakePublicStorage();
        $this->actingAs(User::factory()->create());

        $response = $this->from(route('admin.downloads.create'))
            ->post(route('admin.downloads.store'), $this->downloadAttributes());

        $response->assertRedirect(route('admin.downloads.create'))
            ->assertSessionHasErrors(['file']);

        $this->assertDatabaseCount('publications', 0);
    }

    public function test_download_upload_rejects_unsupported_file_types(): void
    {
        $this->fakePublicStorage();
        $this->actingAs(User::factory()->create());

        $response = $this->from(route('admin.downloads.create'))
            ->post(route('admin.downloads.store'), [
                ...$this->downloadAttributes(),
                'file' => UploadedFile::fake()->create('archive.zip', 100, 'application/zip'),
            ]);

        $response->assertRedirect(route('admin.downloads.create'))
            ->assertSessionHasErrors(['file']);

        $this->assertDatabaseCount('publications', 0);
    }

    public function test_authenticated_admin_can_update_download_item_with_new_file(): void
    {
        $this->fakePublicStorage();
        $this->actingAs(User::factory()->create());

        $initialPath = UploadedFile::fake()->create('old-form.pdf', 150, 'application/pdf')
            ->store('downloads', 'public');

        $download = Publication::factory()->create([
            'title' => 'Initial Form',
            'category' => 'Application Form',
            'file_path' => $initialPath,
            'file_size' => '150.0 KB',
        ]);

        Storage::disk('public')->assertExists($initialPath);

        $response = $this->put(route('admin.downloads.update', $download->id), [
            ...$this->downloadAttributes([
                'title' => 'Updated Form Title',
            ]),
            'file' => UploadedFile::fake()->create('new-form.pdf', 450, 'application/pdf'),
        ]);

        $response->assertRedirect(route('admin.downloads.index'))
            ->assertSessionHasNoErrors();

        $download->refresh();
        $this->assertEquals('Updated Form Title', $download->title);
        $this->assertNotEquals($initialPath, $download->file_path);
        $this->assertEquals('450.0 KB', $download->file_size);

        Storage::disk('public')->assertMissing($initialPath);
        Storage::disk('public')->assertExists($download->file_path);
    }

    public function test_authenticated_admin_can_update_download_without_reuploading_file(): void
    {
        $this->fakePublicStorage();
        $this->actingAs(User::factory()->create());

        $filePath = UploadedFile::fake()->create('existing-form.pdf', 200, 'application/pdf')
            ->store('downloads', 'public');

        $download = Publication::factory()->create([
            'title' => 'Original Form',
            'category' => 'Application Form',
            'file_path' => $filePath,
            'file_size' => '200.0 KB',
        ]);

        $response = $this->put(route('admin.downloads.update', $download->id), [
            ...$this->downloadAttributes([
                'title' => 'Updated Title Only',
            ]),
        ]);

        $response->assertRedirect(route('admin.downloads.index'))
            ->assertSessionHasNoErrors();

        $download->refresh();
        $this->assertEquals('Updated Title Only', $download->title);
        $this->assertEquals($filePath, $download->file_path);
        Storage::disk('public')->assertExists($filePath);
    }

    public function test_deleting_download_item_removes_file_from_storage(): void
    {
        $this->fakePublicStorage();
        $this->actingAs(User::factory()->create());

        $filePath = UploadedFile::fake()->create('to-delete.pdf', 100, 'application/pdf')
            ->store('downloads', 'public');

        $download = Publication::factory()->create([
            'category' => 'Handbook',
            'file_path' => $filePath,
        ]);

        Storage::disk('public')->assertExists($filePath);

        $response = $this->delete(route('admin.downloads.destroy', $download->id));

        $response->assertRedirect(route('admin.downloads.index'))
            ->assertSessionHas('success', 'Download item deleted successfully.');

        $this->assertDatabaseMissing('publications', ['id' => $download->id]);
        Storage::disk('public')->assertMissing($filePath);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function downloadAttributes(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Aswesuma Phase II Appeals Form',
            'category' => 'Application Form',
            'document_number' => 'WBB/APP/2026/01',
            'language' => 'Sinhala',
            'description' => 'Appeals submission form for Aswesuma scheme.',
            'published_date' => '2026-02-01',
            'is_active' => true,
        ], $overrides);
    }

    private function fakePublicStorage(): void
    {
        $diskName = 'download-documents-'.Str::uuid();
        $this->fakeDiskRoot = storage_path('framework/testing/disks/'.$diskName);

        Storage::set('public', Storage::fake($diskName));
    }
}
