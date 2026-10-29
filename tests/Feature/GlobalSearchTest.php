<?php

namespace Tests\Feature;

use App\Models\BoardMember;
use App\Models\Career;
use App\Models\ContactInformation;
use App\Models\Employee;
use App\Models\GovernmentAffiliation;
use App\Models\NewsItem;
use App\Models\Procurement;
use App\Models\Publication;
use App\Models\WelfareScheme;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GlobalSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_finds_published_news_in_all_three_languages_and_hides_unpublished_news(): void
    {
        NewsItem::create([
            'title' => 'English GlobalSearchRecord',
            'title_si' => 'සිංහල ගෝලීය සෙවුම් වාර්තාව',
            'title_ta' => 'தமிழ் உலகளாவிய தேடல் பதிவு',
            'category' => 'Announcement',
            'excerpt' => 'Public English summary',
            'content' => 'Public English content',
            'slug' => 'published-global-search-record',
            'published_date' => '2026-10-25',
            'is_published' => true,
        ]);
        NewsItem::create([
            'title' => 'HiddenGlobalSearchRecord',
            'category' => 'Announcement',
            'excerpt' => 'Unpublished article',
            'content' => 'Unpublished article content',
            'slug' => 'unpublished-global-search-record',
            'published_date' => '2026-10-26',
            'is_published' => false,
        ]);

        foreach ([
            'GlobalSearchRecord' => 'English GlobalSearchRecord',
            'ගෝලීය සෙවුම් වාර්තාව' => 'සිංහල ගෝලීය සෙවුම් වාර්තාව',
            'உலகளாவிய தேடல் பதிவு' => 'தமிழ் உலகளாவிய தேடல் பதிவு',
        ] as $query => $expectedTitle) {
            $this->get(route('search', ['q' => $query]))
                ->assertOk()
                ->assertSee($expectedTitle)
                ->assertDontSee('HiddenGlobalSearchRecord');
        }
    }

    public function test_search_finds_employee_translations_and_static_page_text(): void
    {
        Employee::factory()->create([
            'name' => 'GlobalSearch Employee',
            'name_si' => 'ගෝලීය සෙවුම් සේවකයා',
            'name_ta' => 'உலகளாவிய தேடல் பணியாளர்',
            'designation' => 'Search Officer',
            'designation_si' => 'සෙවුම් නිලධාරී',
            'designation_ta' => 'தேடல் அலுவலர்',
            'is_active' => true,
        ]);
        Employee::factory()->create([
            'name' => 'InactiveGlobalSearchEmployee',
            'name_si' => 'ගෝලීය සෙවුම් සේවකයා',
            'name_ta' => 'உலகளாவிய தேடல் பணியாளர்',
            'designation' => 'Inactive Officer',
            'designation_si' => 'අක්‍රිය නිලධාරී',
            'designation_ta' => 'செயலற்ற அலுவலர்',
            'is_active' => false,
        ]);

        $this->get(route('search', ['q' => 'தேடல் அலுவலர்']))
            ->assertOk()
            ->assertSee('GlobalSearch Employee')
            ->assertSee('தேடல் அலுவலர்')
            ->assertDontSee('InactiveGlobalSearchEmployee');

        foreach ([
            'Welfare Benefits Board' => 'English',
            'සුබසාධක ප්‍රතිලාභ මණ්ඩලය' => 'Sinhala',
            'நலன்புரி நன்மைகள் சபை' => 'Tamil',
        ] as $query => $languageLabel) {
            $this->get(route('search', ['q' => $query]))
                ->assertOk()
                ->assertSee($query)
                ->assertSee($languageLabel)
                ->assertSee(__('site.search.types.page'));
        }
    }

    public function test_search_finds_each_other_active_public_record_type(): void
    {
        $term = 'WbbSearchCoverageToken';

        WelfareScheme::factory()->create([
            'content' => [
                'en' => [
                    'name' => $term,
                    'badge' => 'Benefit',
                    'duration' => 'Ongoing',
                    'description' => 'Public scheme details',
                    'indicators' => [],
                ],
            ],
        ]);
        Publication::factory()->create(['title' => $term]);
        Procurement::factory()->create(['title' => $term]);
        Career::create([
            'title' => $term,
            'description' => 'Public vacancy details',
            'qualifications' => 'Relevant experience',
            'closing_date' => '2026-12-01',
            'is_active' => true,
        ]);
        BoardMember::create([
            'name' => $term,
            'position' => 'member',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        GovernmentAffiliation::factory()->create(['name_en' => $term]);
        ContactInformation::query()->whereKey(1)->update(['address' => $term]);

        $this->get(route('search', ['q' => $term]))
            ->assertOk()
            ->assertSee($term)
            ->assertSee(__('site.search.types.scheme'))
            ->assertSee(__('site.search.types.publication'))
            ->assertSee(__('site.search.types.procurement'))
            ->assertSee(__('site.search.types.career'))
            ->assertSee(__('site.search.types.board_member'))
            ->assertSee(__('site.search.types.affiliation'))
            ->assertSee(__('site.search.types.contact'));
    }

    public function test_search_validates_query_length_and_escapes_matched_content(): void
    {
        NewsItem::create([
            'title' => 'UniqueSearchToken <script>alert("xss")</script>',
            'category' => 'Announcement',
            'excerpt' => 'A public result',
            'content' => 'A public article',
            'slug' => 'escaped-global-search-result',
            'published_date' => '2026-10-25',
            'is_published' => true,
        ]);

        $this->get(route('search', ['q' => 'UniqueSearchToken']))
            ->assertOk()
            ->assertSee('&lt;script&gt;', false)
            ->assertDontSee('<script>alert("xss")</script>', false);

        $this->from(route('search'))
            ->get(route('search', ['q' => str_repeat('a', 192)]))
            ->assertRedirect(route('search'))
            ->assertSessionHasErrors('q');

        $this->get(route('search', ['q' => 'NoMatchingWbbContent']))
            ->assertOk()
            ->assertSee(__('site.search.no_results'));
    }

    public function test_search_treats_like_wildcards_as_literal_characters(): void
    {
        NewsItem::create([
            'title' => 'LiteralPercentRecord 100%',
            'category' => 'Announcement',
            'excerpt' => 'A public result',
            'content' => 'A public article',
            'slug' => 'literal-percent-record',
            'published_date' => '2026-10-25',
            'is_published' => true,
        ]);
        NewsItem::create([
            'title' => 'WildcardPercentRecord 100 percent',
            'category' => 'Announcement',
            'excerpt' => 'A public result',
            'content' => 'A public article',
            'slug' => 'wildcard-percent-record',
            'published_date' => '2026-10-24',
            'is_published' => true,
        ]);
        NewsItem::create([
            'title' => 'Literal_Underscore_Record',
            'category' => 'Announcement',
            'excerpt' => 'A public result',
            'content' => 'A public article',
            'slug' => 'literal-underscore-record',
            'published_date' => '2026-10-23',
            'is_published' => true,
        ]);
        NewsItem::create([
            'title' => 'LiteralXUnderscoreXRecord',
            'category' => 'Announcement',
            'excerpt' => 'A public result',
            'content' => 'A public article',
            'slug' => 'wildcard-underscore-record',
            'published_date' => '2026-10-22',
            'is_published' => true,
        ]);

        $this->get(route('search', ['q' => '%']))
            ->assertOk()
            ->assertSee('LiteralPercentRecord')
            ->assertDontSee('WildcardPercentRecord');

        $this->get(route('search', ['q' => '_']))
            ->assertOk()
            ->assertSee('Literal_Underscore_Record')
            ->assertDontSee('LiteralXUnderscoreXRecord');
    }

    public function test_search_is_available_in_the_public_navigation(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('href="'.route('search').'"', false)
            ->assertSee(__('site.nav.search'));
    }
}
