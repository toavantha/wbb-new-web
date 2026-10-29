<?php

namespace Tests\Feature;

use App\Models\Career;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class CareerDocumentsTest extends TestCase
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

    public function test_authenticated_admin_can_upload_all_vacancy_documents(): void
    {
        $this->fakePublicStorage();
        $this->actingAs(User::factory()->create());

        $response = $this->post(route('admin.careers.store'), [
            ...$this->careerAttributes(),
            'tor_en_file' => UploadedFile::fake()->create('terms-en.pdf', 10, 'application/pdf'),
            'tor_si_file' => UploadedFile::fake()->create('terms-si.pdf', 10, 'application/pdf'),
            'tor_ta_file' => UploadedFile::fake()->create('terms-ta.pdf', 10, 'application/pdf'),
            'details_en_file' => UploadedFile::fake()->create('details-en.docx', 10, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
            'details_si_file' => UploadedFile::fake()->create('details-si.docx', 10, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
            'details_ta_file' => UploadedFile::fake()->create('details-ta.docx', 10, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
            'application_en_file' => UploadedFile::fake()->create('application-en.pdf', 10, 'application/pdf'),
            'application_si_file' => UploadedFile::fake()->create('application-si.pdf', 10, 'application/pdf'),
            'application_ta_file' => UploadedFile::fake()->create('application-ta.pdf', 10, 'application/pdf'),
        ]);
        $response->assertRedirect(route('admin.careers.index'))
            ->assertSessionHasNoErrors();

        $career = Career::query()->where('title', 'Document processing officer')->firstOrFail();

        $this->assertDatabaseHas('careers', [
            'id' => $career->id,
            'tor_file_path' => $career->tor_file_path,
            'details_file_path' => $career->details_file_path,
            'application_file_path' => $career->application_file_path,
            'tor_si_file_path' => $career->tor_si_file_path,
            'tor_ta_file_path' => $career->tor_ta_file_path,
            'details_si_file_path' => $career->details_si_file_path,
            'details_ta_file_path' => $career->details_ta_file_path,
            'application_si_file_path' => $career->application_si_file_path,
            'application_ta_file_path' => $career->application_ta_file_path,
        ]);
        Storage::disk('public')->assertExists([
            $career->tor_file_path,
            $career->details_file_path,
            $career->application_file_path,
            $career->tor_si_file_path,
            $career->tor_ta_file_path,
            $career->details_si_file_path,
            $career->details_ta_file_path,
            $career->application_si_file_path,
            $career->application_ta_file_path,
        ]);
    }

    public function test_authenticated_admin_can_create_a_vacancy_without_documents(): void
    {
        $this->fakePublicStorage();
        $this->actingAs(User::factory()->create());

        $response = $this->post(route('admin.careers.store'), $this->careerAttributes());

        $career = Career::query()->where('title', 'Document processing officer')->firstOrFail();

        $response->assertRedirect(route('admin.careers.index'));
        $this->assertDatabaseHas('careers', [
            'id' => $career->id,
            'tor_file_path' => null,
            'details_file_path' => null,
            'application_file_path' => null,
        ]);
        Storage::disk('public')->assertDirectoryEmpty('vacancies');
    }

    public function test_vacancy_document_upload_requires_cms_authentication(): void
    {
        $this->post(route('admin.careers.store'), $this->careerAttributes())
            ->assertRedirect(route('admin.login'));

        $this->assertDatabaseCount('careers', 0);
    }

    public function test_admin_career_forms_include_optional_document_uploads(): void
    {
        $this->fakePublicStorage();
        $this->actingAs(User::factory()->create());
        $englishTorPath = 'vacancies/en/tor/current.pdf';
        $sinhalaTorPath = 'vacancies/si/tor/current.pdf';
        $tamilTorPath = 'vacancies/ta/tor/current.pdf';
        Storage::disk('public')->put($englishTorPath, 'English TOR');
        Storage::disk('public')->put($sinhalaTorPath, 'Sinhala TOR');
        Storage::disk('public')->put($tamilTorPath, 'Tamil TOR');
        $career = Career::create($this->careerAttributes([
            'tor_file_path' => $englishTorPath,
            'tor_si_file_path' => $sinhalaTorPath,
            'tor_ta_file_path' => $tamilTorPath,
        ]));

        $createResponse = $this->get(route('admin.careers.create'))
            ->assertOk()
            ->assertSee('enctype="multipart/form-data"', false)
            ->assertDontSee('name="attachment_url"', false);

        $editResponse = $this->get(route('admin.careers.edit', $career))
            ->assertOk()
            ->assertSee('enctype="multipart/form-data"', false)
            ->assertDontSee('name="attachment_url"', false)
            ->assertSee('Leave a field empty to keep its current document.');

        foreach (['tor', 'details', 'application'] as $documentType) {
            foreach (['en', 'si', 'ta'] as $locale) {
                $inputName = "name=\"{$documentType}_{$locale}_file\"";

                $this->assertStringContainsString($inputName, $createResponse->getContent());
                $this->assertStringContainsString($inputName, $editResponse->getContent());
            }
        }

        $editResponse->assertSee('View current English document')
            ->assertSee('View current Sinhala document')
            ->assertSee('View current Tamil document')
            ->assertSee(Storage::disk('public')->url($englishTorPath), false)
            ->assertSee(Storage::disk('public')->url($sinhalaTorPath), false)
            ->assertSee(Storage::disk('public')->url($tamilTorPath), false);
    }

    public function test_admin_update_replaces_selected_documents_and_keeps_others(): void
    {
        $this->fakePublicStorage();
        $this->actingAs(User::factory()->create());
        $oldTorPath = 'vacancies/en/tor/old.pdf';
        $detailsPath = 'vacancies/en/details/current.pdf';
        $sinhalaTorPath = 'vacancies/si/tor/current.pdf';
        Storage::disk('public')->put($oldTorPath, 'Old TOR');
        Storage::disk('public')->put($detailsPath, 'Current details');
        Storage::disk('public')->put($sinhalaTorPath, 'Sinhala TOR');
        $career = Career::create($this->careerAttributes([
            'tor_file_path' => $oldTorPath,
            'details_file_path' => $detailsPath,
            'tor_si_file_path' => $sinhalaTorPath,
        ]));

        $response = $this->put(route('admin.careers.update', $career), [
            ...$this->careerAttributes(['title' => 'Updated vacancy']),
            'is_active' => '1',
            'tor_en_file' => UploadedFile::fake()->create('new-terms.pdf', 10, 'application/pdf'),
        ]);

        $career->refresh();

        $response->assertRedirect(route('admin.careers.index'));
        $this->assertSame('Updated vacancy', $career->title);
        $this->assertNotSame($oldTorPath, $career->tor_file_path);
        $this->assertSame($detailsPath, $career->details_file_path);
        $this->assertSame($sinhalaTorPath, $career->tor_si_file_path);
        Storage::disk('public')->assertMissing($oldTorPath);
        Storage::disk('public')->assertExists($career->tor_file_path);
        Storage::disk('public')->assertExists($detailsPath);
        Storage::disk('public')->assertExists($sinhalaTorPath);
    }

    public function test_admin_deletion_removes_vacancy_documents(): void
    {
        $this->fakePublicStorage();
        $this->actingAs(User::factory()->create());
        $torPath = 'vacancies/en/tor/terms.pdf';
        $applicationPath = 'vacancies/en/application/application.docx';
        $tamilDetailsPath = 'vacancies/ta/details/details.pdf';
        Storage::disk('public')->put($torPath, 'TOR');
        Storage::disk('public')->put($applicationPath, 'Application');
        Storage::disk('public')->put($tamilDetailsPath, 'Tamil details');
        $career = Career::create($this->careerAttributes([
            'tor_file_path' => $torPath,
            'application_file_path' => $applicationPath,
            'details_ta_file_path' => $tamilDetailsPath,
        ]));

        $response = $this->delete(route('admin.careers.destroy', $career));

        $response->assertRedirect(route('admin.careers.index'));
        $this->assertDatabaseMissing('careers', ['id' => $career->id]);
        Storage::disk('public')->assertMissing([$torPath, $applicationPath, $tamilDetailsPath]);
    }

    public function test_public_can_download_an_uploaded_vacancy_document(): void
    {
        $this->fakePublicStorage();
        $documentPath = 'vacancies/en/tor/terms.pdf';
        Storage::disk('public')->put($documentPath, 'TOR document');
        $career = Career::create($this->careerAttributes(['tor_file_path' => $documentPath]));

        $response = $this->get(route('careers.documents.download', [$career, 'tor']));

        $response->assertOk()
            ->assertDownload('terms.pdf');
        $this->assertSame('TOR document', $response->streamedContent());
    }

    public function test_public_document_download_uses_the_selected_page_language(): void
    {
        $this->fakePublicStorage();
        $englishPath = 'vacancies/en/tor/terms.pdf';
        $sinhalaPath = 'vacancies/si/tor/terms.pdf';
        $tamilPath = 'vacancies/ta/tor/terms.pdf';
        Storage::disk('public')->put($englishPath, 'English TOR');
        Storage::disk('public')->put($sinhalaPath, 'Sinhala TOR');
        Storage::disk('public')->put($tamilPath, 'Tamil TOR');
        $career = Career::create($this->careerAttributes([
            'tor_file_path' => $englishPath,
            'tor_si_file_path' => $sinhalaPath,
            'tor_ta_file_path' => $tamilPath,
        ]));

        $englishResponse = $this->withSession(['locale' => 'en'])
            ->get(route('careers.documents.download', [$career, 'tor']));
        $sinhalaResponse = $this->withSession(['locale' => 'si'])
            ->get(route('careers.documents.download', [$career, 'tor']));
        $tamilResponse = $this->withSession(['locale' => 'ta'])
            ->get(route('careers.documents.download', [$career, 'tor']));

        $this->assertSame('English TOR', $englishResponse->streamedContent());
        $this->assertSame('Sinhala TOR', $sinhalaResponse->streamedContent());
        $this->assertSame('Tamil TOR', $tamilResponse->streamedContent());

        $this->withSession(['locale' => 'si'])
            ->get(route('careers'))
            ->assertOk()
            ->assertSee('TOR බාගත කරන්න');
        $this->withSession(['locale' => 'ta'])
            ->get(route('careers'))
            ->assertOk()
            ->assertSee('TOR பதிவிறக்கவும்');
    }

    public function test_details_and_application_downloads_return_their_own_documents_in_each_language(): void
    {
        $this->fakePublicStorage();
        $documentContents = [
            'en' => ['details' => 'English details document', 'application' => 'English application document'],
            'si' => ['details' => 'Sinhala details document', 'application' => 'Sinhala application document'],
            'ta' => ['details' => 'Tamil details document', 'application' => 'Tamil application document'],
        ];
        $careerAttributes = $this->careerAttributes();

        foreach ($documentContents as $locale => $documents) {
            foreach ($documents as $documentType => $contents) {
                $path = "vacancies/{$locale}/{$documentType}/{$documentType}.pdf";
                Storage::disk('public')->put($path, $contents);

                $field = $locale === 'en'
                    ? "{$documentType}_file_path"
                    : "{$documentType}_{$locale}_file_path";
                $careerAttributes[$field] = $path;
            }
        }

        $career = Career::create($careerAttributes);

        foreach ($documentContents as $locale => $documents) {
            foreach ($documents as $documentType => $contents) {
                $response = $this->withSession(['locale' => $locale])
                    ->get(route('careers.documents.download', [$career, $documentType]));

                $response->assertOk()
                    ->assertDownload("{$documentType}.pdf");
                $this->assertSame($contents, $response->streamedContent());
            }
        }
    }

    public function test_missing_document_in_selected_language_does_not_fall_back_to_english(): void
    {
        $this->fakePublicStorage();
        $englishPath = 'vacancies/en/application/application.pdf';
        Storage::disk('public')->put($englishPath, 'English Application');
        $career = Career::create($this->careerAttributes([
            'application_file_path' => $englishPath,
        ]));

        $this->withSession(['locale' => 'si'])
            ->from(route('careers'))
            ->get(route('careers.documents.download', [$career, 'application']))
            ->assertRedirect(route('careers'))
            ->assertSessionHas('error', 'There are no documents uploaded');
    }

    public function test_missing_document_redirects_with_the_exact_message(): void
    {
        $career = Career::create($this->careerAttributes());

        $this->from(route('careers'))
            ->get(route('careers.documents.download', [$career, 'tor']))
            ->assertRedirect(route('careers'))
            ->assertSessionHas('error', 'There are no documents uploaded');

        $this->get(route('careers'))
            ->assertOk()
            ->assertSee('There are no documents uploaded');
    }

    public function test_document_missing_from_storage_redirects_with_the_exact_message(): void
    {
        $this->fakePublicStorage();
        $career = Career::create($this->careerAttributes([
            'details_si_file_path' => 'vacancies/si/details/missing.pdf',
        ]));

        $this->from(route('careers'))
            ->get(route('careers.documents.download', [$career, 'details']))
            ->assertRedirect(route('careers'))
            ->assertSessionHas('error', 'There are no documents uploaded');
    }

    public function test_careers_page_links_to_each_document_without_legacy_attachment_button(): void
    {
        $career = Career::create($this->careerAttributes([
            'attachment_url' => 'https://example.gov.lk/legacy-vacancy.pdf',
        ]));

        $response = $this->get(route('careers'));

        $response->assertOk()
            ->assertSee(route('careers.documents.download', [$career, 'tor']), false)
            ->assertSee(route('careers.documents.download', [$career, 'details']), false)
            ->assertSee(route('careers.documents.download', [$career, 'application']), false)
            ->assertDontSee('https://example.gov.lk/legacy-vacancy.pdf', false)
            ->assertDontSee('Existing vacancy document')
            ->assertSee('Download TOR')
            ->assertSee('Download Details')
            ->assertSee('Download Application');
    }

    public function test_inactive_vacancy_documents_are_not_publicly_available(): void
    {
        $career = Career::create($this->careerAttributes(['is_active' => false]));

        $this->get(route('careers.documents.download', [$career, 'tor']))
            ->assertNotFound();
    }

    public function test_vacancy_document_upload_rejects_unsupported_file_types(): void
    {
        $this->fakePublicStorage();
        $this->actingAs(User::factory()->create());

        $response = $this->from(route('admin.careers.create'))
            ->post(route('admin.careers.store'), [
                ...$this->careerAttributes(),
                'tor_en_file' => UploadedFile::fake()->create('terms.txt', 10, 'text/plain'),
            ]);

        $response->assertRedirect(route('admin.careers.create'))
            ->assertSessionHasErrors([
                'tor_en_file' => 'The tor en file field must be a file of type: pdf, docx.',
            ]);
        $this->assertDatabaseCount('careers', 0);
        Storage::disk('public')->assertDirectoryEmpty('vacancies');
    }

    public function test_vacancy_document_upload_rejects_files_over_10_mb(): void
    {
        $this->fakePublicStorage();
        $this->actingAs(User::factory()->create());

        $response = $this->from(route('admin.careers.create'))
            ->post(route('admin.careers.store'), [
                ...$this->careerAttributes(),
                'tor_ta_file' => UploadedFile::fake()->create('terms.pdf', 10241, 'application/pdf'),
            ]);

        $response->assertRedirect(route('admin.careers.create'))
            ->assertSessionHasErrors([
                'tor_ta_file' => 'The tor ta file field must not be greater than 10240 kilobytes.',
            ]);
        $this->assertDatabaseCount('careers', 0);
        Storage::disk('public')->assertDirectoryEmpty('vacancies');
    }

    private function careerAttributes(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Document processing officer',
            'department' => 'Operations',
            'employment_type' => 'Full-Time',
            'location' => 'Colombo',
            'description' => 'Review benefit applications.',
            'qualifications' => 'Relevant degree and experience.',
            'closing_date' => '2030-12-31',
            'is_active' => true,
        ], $overrides);
    }

    private function fakePublicStorage(): void
    {
        $diskName = 'career-documents-'.Str::uuid();
        $this->fakeDiskRoot = storage_path('framework/testing/disks/'.$diskName);

        Storage::set('public', Storage::fake($diskName));
    }
}
