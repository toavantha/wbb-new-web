<?php

namespace Tests\Feature;

use App\Models\Procurement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProcurementManagementTest extends TestCase
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

    public function test_public_can_view_procurements_page_in_all_three_languages(): void
    {
        $today = Carbon::today();

        $activeProcurement = Procurement::factory()->create([
            'title' => 'Server Hardware Supply',
            'title_si' => 'සර්වර් උපකරණ සැපයීම',
            'title_ta' => 'சேவையக வன்பொருள் வழங்கல்',
            'category' => 'Procurement Notice',
            'deadline_date' => $today->copy()->addDays(10),
            'is_active' => true,
        ]);

        // English
        $responseEn = $this->withSession(['locale' => 'en'])->get(route('procurements'));
        $responseEn->assertOk()
            ->assertSee('Procurements & Tenders')
            ->assertSee('Server Hardware Supply');

        // Sinhala
        $responseSi = $this->withSession(['locale' => 'si'])->get(route('procurements'));
        $responseSi->assertOk()
            ->assertSee('ප්‍රසම්පාදන සහ ටෙන්ඩර්')
            ->assertSee('සර්වර් උපකරණ සැපයීම');

        // Tamil
        $responseTa = $this->withSession(['locale' => 'ta'])->get(route('procurements'));
        $responseTa->assertOk()
            ->assertSee('கொள்முதல் மற்றும் டெண்டர்கள்')
            ->assertSee('சேவையக வன்பொருள் வழங்கல்');
    }

    public function test_expired_notices_move_into_expired_category_and_current_remain_in_current(): void
    {
        $today = Carbon::today();

        $currentNotice = Procurement::factory()->create([
            'title' => 'Active Fiber Network Tender',
            'category' => 'Tender Document',
            'deadline_date' => $today->copy()->addDays(15),
            'is_active' => true,
        ]);

        $expiredNotice = Procurement::factory()->create([
            'title' => 'Expired Office Relocation Tender',
            'category' => 'Tender Document',
            'deadline_date' => $today->copy()->subDays(5),
            'is_active' => true,
        ]);

        // When viewing current notices (default)
        $responseCurrent = $this->get(route('procurements', ['status' => 'current']));
        $responseCurrent->assertOk()
            ->assertSee('Active Fiber Network Tender')
            ->assertDontSee('Expired Office Relocation Tender');

        // When viewing expired notices
        $responseExpired = $this->get(route('procurements', ['status' => 'expired']));
        $responseExpired->assertOk()
            ->assertSee('Expired Office Relocation Tender')
            ->assertDontSee('Active Fiber Network Tender');
    }

    public function test_filtering_by_four_categories(): void
    {
        $today = Carbon::today();

        $notice = Procurement::factory()->create([
            'title' => 'Notice Category Item',
            'category' => 'Procurement Notice',
            'deadline_date' => $today->copy()->addDays(10),
            'is_active' => true,
        ]);

        $tender = Procurement::factory()->create([
            'title' => 'Tender Category Item',
            'category' => 'Tender Document',
            'deadline_date' => $today->copy()->addDays(10),
            'is_active' => true,
        ]);

        $tor = Procurement::factory()->create([
            'title' => 'TOR Category Item',
            'category' => 'TOR',
            'deadline_date' => $today->copy()->addDays(10),
            'is_active' => true,
        ]);

        $other = Procurement::factory()->create([
            'title' => 'Other Category Item',
            'category' => 'Other',
            'deadline_date' => $today->copy()->addDays(10),
            'is_active' => true,
        ]);

        $responseTor = $this->get(route('procurements', ['category' => 'TOR']));
        $responseTor->assertOk()
            ->assertSee('TOR Category Item')
            ->assertDontSee('Notice Category Item')
            ->assertDontSee('Tender Category Item')
            ->assertDontSee('Other Category Item');
    }

    public function test_procurement_search_treats_like_wildcards_as_literal_characters(): void
    {
        Procurement::factory()->create([
            'title' => 'Tender 100% complete',
            'deadline_date' => Carbon::today()->addDays(10),
            'is_active' => true,
        ]);
        Procurement::factory()->create([
            'title' => 'Tender 100 percent complete',
            'deadline_date' => Carbon::today()->addDays(10),
            'is_active' => true,
        ]);
        Procurement::factory()->create([
            'title' => 'Bid_Award_Notice',
            'deadline_date' => Carbon::today()->addDays(10),
            'is_active' => true,
        ]);
        Procurement::factory()->create([
            'title' => 'BidXAwardXNotice',
            'deadline_date' => Carbon::today()->addDays(10),
            'is_active' => true,
        ]);

        $this->get(route('procurements', ['search' => '%']))
            ->assertOk()
            ->assertSee('Tender 100% complete')
            ->assertDontSee('Tender 100 percent complete');

        $this->get(route('procurements', ['search' => '_']))
            ->assertOk()
            ->assertSee('Bid_Award_Notice')
            ->assertDontSee('BidXAwardXNotice');
    }

    public function test_admin_procurement_search_treats_like_wildcards_as_literal_characters(): void
    {
        $this->actingAs(User::factory()->create());
        Procurement::factory()->create(['title' => 'Admin Tender 100% complete']);
        Procurement::factory()->create(['title' => 'Admin Tender 100 percent complete']);
        Procurement::factory()->create(['title' => 'Admin Bid_Award_Notice']);
        Procurement::factory()->create(['title' => 'Admin BidXAwardXNotice']);

        $this->get(route('admin.procurements.index', ['search' => '%']))
            ->assertOk()
            ->assertSee('Admin Tender 100% complete')
            ->assertSee('data-open-extend-modal', false)
            ->assertDontSee('onclick=', false)
            ->assertDontSee('onsubmit=', false)
            ->assertDontSee('<script>', false)
            ->assertDontSee('Admin Tender 100 percent complete');

        $this->get(route('admin.procurements.index', ['search' => '_']))
            ->assertOk()
            ->assertSee('Admin Bid_Award_Notice')
            ->assertDontSee('Admin BidXAwardXNotice');
    }

    public function test_english_content_is_default_fallback_when_sinhala_and_tamil_are_omitted(): void
    {
        $today = Carbon::today();

        $procurement = Procurement::factory()->create([
            'title' => 'English Only Procurement Notice',
            'title_si' => null,
            'title_ta' => null,
            'description' => 'Detailed English technical specifications.',
            'description_si' => null,
            'description_ta' => null,
            'deadline_date' => $today->copy()->addDays(20),
            'is_active' => true,
        ]);

        $this->assertEquals('English Only Procurement Notice', $procurement->localizedTitle('en'));
        $this->assertEquals('English Only Procurement Notice', $procurement->localizedTitle('si'));
        $this->assertEquals('English Only Procurement Notice', $procurement->localizedTitle('ta'));

        $this->assertEquals('Detailed English technical specifications.', $procurement->localizedDescription('en'));
        $this->assertEquals('Detailed English technical specifications.', $procurement->localizedDescription('si'));
        $this->assertEquals('Detailed English technical specifications.', $procurement->localizedDescription('ta'));

        // Check on Sinhala and Tamil public pages
        $responseSi = $this->withSession(['locale' => 'si'])->get(route('procurements'));
        $responseSi->assertOk()->assertSee('English Only Procurement Notice');

        $responseTa = $this->withSession(['locale' => 'ta'])->get(route('procurements'));
        $responseTa->assertOk()->assertSee('English Only Procurement Notice');
    }

    public function test_multilingual_content_appears_separately_when_published(): void
    {
        $today = Carbon::today();

        $procurement = Procurement::factory()->create([
            'title' => 'National Bidding for Printing',
            'title_si' => 'මුද්‍රණ කටයුතු සඳහා ජාතික ලංසු කැඳවීම',
            'title_ta' => 'அச்சிடுவதற்கான தேசிய ஏலக் கோரல்',
            'deadline_date' => $today->copy()->addDays(20),
            'is_active' => true,
        ]);

        $this->assertEquals('National Bidding for Printing', $procurement->localizedTitle('en'));
        $this->assertEquals('මුද්‍රණ කටයුතු සඳහා ජාතික ලංසු කැඳවීම', $procurement->localizedTitle('si'));
        $this->assertEquals('அச்சிடுவதற்கான தேசிய ஏலக் கோரல்', $procurement->localizedTitle('ta'));

        $responseSi = $this->withSession(['locale' => 'si'])->get(route('procurements'));
        $responseSi->assertOk()
            ->assertSee('මුද්‍රණ කටයුතු සඳහා ජාතික ලංසු කැඳවීම')
            ->assertDontSee('National Bidding for Printing');

        $responseTa = $this->withSession(['locale' => 'ta'])->get(route('procurements'));
        $responseTa->assertOk()
            ->assertSee('அச்சிடுவதற்கான தேசிய ஏலக் கோரல்')
            ->assertDontSee('National Bidding for Printing');
    }

    public function test_document_download_falls_back_to_english_when_localized_file_absent(): void
    {
        $this->fakePublicStorage();

        $filePath = UploadedFile::fake()->create('tender_en.pdf', 300, 'application/pdf')
            ->store('procurements/en', 'public');

        $procurement = Procurement::factory()->create([
            'file_path' => $filePath,
            'file_path_si' => null,
            'file_path_ta' => null,
            'is_active' => true,
        ]);

        // Requesting download in Sinhala locale should fallback to English file
        $response = $this->withSession(['locale' => 'si'])->get(route('procurements.download', [$procurement, 'si']));
        $response->assertOk();
    }

    public function test_document_download_serves_localized_file_when_present(): void
    {
        $this->fakePublicStorage();

        $filePathEn = UploadedFile::fake()->create('tender_en.pdf', 300, 'application/pdf')
            ->store('procurements/en', 'public');
        $filePathSi = UploadedFile::fake()->create('tender_si.pdf', 350, 'application/pdf')
            ->store('procurements/si', 'public');

        $procurement = Procurement::factory()->create([
            'file_path' => $filePathEn,
            'file_path_si' => $filePathSi,
            'is_active' => true,
        ]);

        $response = $this->get(route('procurements.download', [$procurement, 'si']));
        $response->assertOk();
    }

    public function test_document_can_be_viewed_online_in_browser(): void
    {
        $this->fakePublicStorage();

        $filePath = UploadedFile::fake()->create('tender_notice.pdf', 250, 'application/pdf')
            ->store('procurements/en', 'public');

        $procurement = Procurement::factory()->create([
            'file_path' => $filePath,
            'is_active' => true,
        ]);

        $response = $this->get(route('procurements.view', [$procurement, 'en']));
        $response->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');

        $contentDisposition = $response->headers->get('Content-Disposition');
        $this->assertStringContainsString('inline', $contentDisposition);

        // Verify "View Online" link is present in the public procurements page
        $pageResponse = $this->get(route('procurements'));
        $pageResponse->assertOk()
            ->assertSee(route('procurements.view', [$procurement, 'en']))
            ->assertSee('View Online');
    }

    public function test_document_view_online_falls_back_to_english_when_localized_absent(): void
    {
        $this->fakePublicStorage();

        $filePath = UploadedFile::fake()->create('tender_notice.pdf', 250, 'application/pdf')
            ->store('procurements/en', 'public');

        $procurement = Procurement::factory()->create([
            'file_path' => $filePath,
            'file_path_si' => null,
            'is_active' => true,
        ]);

        $response = $this->withSession(['locale' => 'si'])->get(route('procurements.view', [$procurement, 'si']));
        $response->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');

        $this->assertStringContainsString('inline', $response->headers->get('Content-Disposition'));
    }

    public function test_home_page_displays_latest_procurements_in_all_three_languages(): void
    {
        $today = Carbon::today();

        Procurement::factory()->create([
            'title' => 'Latest Cloud Servers Procurement',
            'title_si' => 'නවතම ක්ලවුඩ් සර්වර් ප්‍රසම්පාදනය',
            'title_ta' => 'சமீபத்திய கிளவுட் சேவையக கொள்முதல்',
            'category' => 'Procurement Notice',
            'deadline_date' => $today->copy()->addDays(25),
            'is_active' => true,
        ]);

        // Home English
        $responseEn = $this->withSession(['locale' => 'en'])->get(route('home'));
        $responseEn->assertOk()
            ->assertSee('Latest Procurements & Tenders')
            ->assertSee('Latest Cloud Servers Procurement');

        // Home Sinhala
        $responseSi = $this->withSession(['locale' => 'si'])->get(route('home'));
        $responseSi->assertOk()
            ->assertSee('නවතම ප්‍රසම්පාදන සහ ටෙන්ඩර්')
            ->assertSee('නවතම ක්ලවුඩ් සර්වර් ප්‍රසම්පාදනය');

        // Home Tamil
        $responseTa = $this->withSession(['locale' => 'ta'])->get(route('home'));
        $responseTa->assertOk()
            ->assertSee('சமீபத்திய கொள்முதல் மற்றும் டெண்டர்கள்')
            ->assertSee('சமீபத்திய கிளவுட் சேவையக கொள்முதல்');
    }

    public function test_cms_admin_can_create_procurement_with_multilingual_files_and_categories(): void
    {
        $this->fakePublicStorage();
        $this->actingAs(User::factory()->create());

        $response = $this->post(route('admin.procurements.store'), [
            'title' => 'Procurement of Biometric Verification Devices',
            'title_si' => 'ජෛවමිතික සත්‍යාපන උපාංග ප්‍රසම්පාදනය',
            'title_ta' => 'உயிரியல் அளவீட்டு சரிபார்ப்பு சாதனங்கள் கொள்முதல்',
            'reference_no' => 'WBB/BIO/2026/09',
            'category' => 'TOR',
            'description' => 'Supply of 1,000 fingerprint and iris scanning devices.',
            'published_date' => '2026-10-01',
            'deadline_date' => '2026-11-20',
            'file_en' => UploadedFile::fake()->create('devices_en.pdf', 400, 'application/pdf'),
            'file_si' => UploadedFile::fake()->create('devices_si.pdf', 420, 'application/pdf'),
            'is_active' => true,
        ]);

        $response->assertRedirect(route('admin.procurements.index'))
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success', 'Procurement created successfully.');

        $procurement = Procurement::where('reference_no', 'WBB/BIO/2026/09')->firstOrFail();

        $this->assertEquals('TOR', $procurement->category);
        $this->assertNotNull($procurement->file_path);
        $this->assertNotNull($procurement->file_path_si);
        $this->assertNull($procurement->file_path_ta);
        $this->assertEquals('2026-11-20', $procurement->deadline_date->format('Y-m-d'));

        Storage::disk('public')->assertExists($procurement->file_path);
        Storage::disk('public')->assertExists($procurement->file_path_si);

        $this->assertDatabaseHas('cms_activity_logs', [
            'subject_type' => 'procurement',
            'subject_id' => $procurement->id,
            'action' => 'created',
        ]);
    }

    public function test_cms_admin_can_extend_notice_deadline_and_it_moves_up_accordingly(): void
    {
        $this->actingAs(User::factory()->create());
        $today = Carbon::today();

        // Expired notice
        $procurement = Procurement::factory()->create([
            'title' => 'Software Development Services',
            'deadline_date' => $today->copy()->subDays(5),
            'original_deadline_date' => null,
            'is_active' => true,
        ]);

        $this->assertTrue($procurement->isExpired());

        // Quick extend deadline route
        $newDeadline = $today->copy()->addDays(30)->format('Y-m-d');
        $oldDeadline = $procurement->deadline_date->format('Y-m-d');

        $response = $this->patch(route('admin.procurements.extend-deadline', $procurement->id), [
            'new_deadline_date' => $newDeadline,
        ]);

        $response->assertRedirect();

        $procurement->refresh();

        $this->assertFalse($procurement->isExpired());
        $this->assertTrue($procurement->isExtended());
        $this->assertEquals($newDeadline, $procurement->deadline_date->format('Y-m-d'));
        $this->assertEquals($oldDeadline, $procurement->original_deadline_date->format('Y-m-d'));

        // Check that it now appears in current notices query, moving up in the list
        $currentNotices = Procurement::where('is_active', true)
            ->where('deadline_date', '>=', $today)
            ->orderBy('deadline_date', 'desc')
            ->get();

        $this->assertTrue($currentNotices->contains('id', $procurement->id));

        $this->assertDatabaseHas('cms_activity_logs', [
            'subject_type' => 'procurement',
            'subject_id' => $procurement->id,
            'action' => 'updated',
        ]);
    }

    public function test_cms_admin_can_update_and_delete_procurement(): void
    {
        $this->fakePublicStorage();
        $this->actingAs(User::factory()->create());

        $filePath = UploadedFile::fake()->create('tender.pdf', 200, 'application/pdf')
            ->store('procurements/en', 'public');

        $procurement = Procurement::factory()->create([
            'title' => 'Original Title',
            'category' => 'Other',
            'file_path' => $filePath,
            'is_active' => true,
        ]);

        // Update
        $responseUpdate = $this->put(route('admin.procurements.update', $procurement->id), [
            'title' => 'Updated Title',
            'category' => 'Procurement Notice',
            'published_date' => '2026-10-10',
            'deadline_date' => '2026-12-01',
            'is_active' => true,
        ]);

        $responseUpdate->assertRedirect(route('admin.procurements.index'))
            ->assertSessionHas('success', 'Procurement updated successfully.');

        $this->assertDatabaseHas('procurements', [
            'id' => $procurement->id,
            'title' => 'Updated Title',
            'category' => 'Procurement Notice',
        ]);

        // Delete
        $responseDelete = $this->delete(route('admin.procurements.destroy', $procurement->id));
        $responseDelete->assertRedirect(route('admin.procurements.index'))
            ->assertSessionHas('success', 'Procurement deleted successfully.');

        $this->assertDatabaseMissing('procurements', ['id' => $procurement->id]);
        Storage::disk('public')->assertMissing($filePath);
    }

    private function fakePublicStorage(): void
    {
        $diskName = 'procurement-documents-'.Str::uuid();
        $this->fakeDiskRoot = storage_path('framework/testing/disks/'.$diskName);

        Storage::set('public', Storage::fake($diskName));
    }
}
