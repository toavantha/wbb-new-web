<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WelfareScheme;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WelfareSchemeManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_schemes_page_displays_active_schemes_in_display_order_and_selected_language(): void
    {
        $firstScheme = WelfareScheme::factory()->create([
            'content' => $this->schemeContent([
                'en' => ['name' => 'First Scheme'],
                'si' => ['name' => 'පළමු යෝජනා ක්‍රමය'],
                'ta' => ['name' => 'முதல் திட்டம்'],
            ]),
            'sort_order' => 10,
        ]);
        WelfareScheme::factory()->create([
            'content' => $this->schemeContent([
                'en' => ['name' => 'Hidden Scheme'],
                'si' => ['name' => 'සඟවා ඇති යෝජනා ක්‍රමය'],
                'ta' => ['name' => 'மறைக்கப்பட்ட திட்டம்'],
            ]),
            'sort_order' => 11,
            'is_active' => false,
        ]);
        $secondScheme = WelfareScheme::factory()->create([
            'content' => $this->schemeContent([
                'en' => ['name' => 'Second Scheme'],
                'si' => ['name' => 'දෙවන යෝජනා ක්‍රමය'],
                'ta' => ['name' => 'இரண்டாவது திட்டம்'],
            ]),
            'sort_order' => 12,
            'monthly_allowance' => 2500,
        ]);

        $this->withSession(['locale' => 'si']);
        $response = $this->get(route('schemes'));

        $response->assertOk()
            ->assertSee($firstScheme->content['si']['name'])
            ->assertSee($secondScheme->content['si']['name'])
            ->assertSee('Rs. 2,500 / month')
            ->assertDontSee('සඟවා ඇති යෝජනා ක්‍රමය');
        $this->assertLessThan(
            strpos($response->getContent(), $secondScheme->content['si']['name']),
            strpos($response->getContent(), $firstScheme->content['si']['name']),
        );
    }

    public function test_public_schemes_page_keeps_the_five_existing_scheme_cards_and_allowances(): void
    {
        $response = $this->get(route('schemes'));

        $response->assertOk()
            ->assertSee('Aswesuma - Severely Poor')
            ->assertSee('Aswesuma - Poor')
            ->assertSee('Elderly Allowance (Senior Citizens 70+)')
            ->assertSee('Persons with Disabilities Allowance')
            ->assertSee('CKDu (Kidney Disease) Patient Allowance')
            ->assertSee('Rs. 15,000 / month')
            ->assertSee('Rs. 8,500 / month')
            ->assertSee('Rs. 5,000 / month')
            ->assertSee('Rs. 7,500 / month');
        $this->assertDatabaseCount('welfare_schemes', 5);
    }

    public function test_home_page_displays_current_active_scheme_content_and_allowances(): void
    {
        $scheme = WelfareScheme::factory()->create([
            'content' => $this->schemeContent([
                'en' => ['name' => 'Previous Homepage Scheme'],
            ]),
            'monthly_allowance' => 4000,
            'sort_order' => 1,
        ]);
        $scheme->update([
            'content' => $this->schemeContent([
                'en' => [
                    'name' => 'Updated Homepage Scheme',
                    'badge' => 'Updated Category',
                    'duration' => 'Updated duration',
                    'description' => 'Current CMS description.',
                ],
            ]),
            'monthly_allowance' => 12345,
        ]);
        WelfareScheme::factory()->create([
            'content' => $this->schemeContent([
                'en' => ['name' => 'Inactive Homepage Scheme'],
            ]),
            'is_active' => false,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Updated Homepage Scheme')
            ->assertSee('Updated Category')
            ->assertSee('Updated duration')
            ->assertSee('Current CMS description.')
            ->assertSee('Rs. 12,345 / month')
            ->assertDontSee('Previous Homepage Scheme')
            ->assertDontSee('Inactive Homepage Scheme');
    }

    public function test_home_page_shows_nic_requirement_in_each_language(): void
    {
        foreach ([
            'en' => 'A National Identity Card (NIC) is mandatory to receive Aswesuma benefits.',
            'si' => 'අස්වැසුම ප්‍රතිලාභ ලබා ගැනීම සඳහා ජාතික හැඳුනුම්පත (NIC) අනිවාර්ය වේ.',
            'ta' => 'அஸ்வெசும நலன்களைப் பெற தேசிய அடையாள அட்டை (NIC) கட்டாயமாகும்.',
        ] as $locale => $notice) {
            $this->withSession(['locale' => $locale])
                ->get(route('home'))
                ->assertOk()
                ->assertSee($notice);
        }
    }

    public function test_welfare_scheme_management_requires_authentication(): void
    {
        $this->get(route('admin.welfare-schemes.index'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_authenticated_editor_can_create_edit_and_delete_a_welfare_scheme(): void
    {
        $editor = User::factory()->create([
            'role' => 'editor',
            'is_approved' => true,
        ]);
        $this->actingAs($editor);

        $this->get(route('admin.welfare-schemes.index'))
            ->assertOk()
            ->assertSee('Aswesuma - Severely Poor');
        $this->get(route('admin.welfare-schemes.create'))
            ->assertOk()
            ->assertSee('English Content')
            ->assertSee('Sinhala Content')
            ->assertSee('Tamil Content');

        $response = $this->post(route('admin.welfare-schemes.store'), $this->validSchemeInput([
            'content' => $this->schemeContent([
                'en' => ['name' => 'New Welfare Scheme'],
                'si' => ['name' => 'නව සුබසාධන යෝජනා ක්‍රමය'],
                'ta' => ['name' => 'புதிய நலன்புரித் திட்டம்'],
            ]),
            'monthly_allowance' => 6000,
            'sort_order' => 6,
        ]));
        $scheme = WelfareScheme::query()
            ->where('content->en->name', 'New Welfare Scheme')
            ->firstOrFail();

        $response->assertRedirect(route('admin.welfare-schemes.index'));
        $this->assertDatabaseHas('welfare_schemes', [
            'id' => $scheme->id,
            'monthly_allowance' => 6000,
            'sort_order' => 6,
            'is_active' => 1,
        ]);
        $this->assertDatabaseHas('cms_activity_logs', [
            'subject_type' => 'welfare_scheme',
            'subject_id' => $scheme->id,
            'action' => 'created',
        ]);

        $this->get(route('admin.welfare-schemes.edit', $scheme))
            ->assertOk()
            ->assertSee('New Welfare Scheme');

        $this->put(route('admin.welfare-schemes.update', $scheme), $this->validSchemeInput([
            'content' => $this->schemeContent([
                'en' => ['name' => 'Updated Welfare Scheme'],
            ]),
            'monthly_allowance' => 8000,
            'sort_order' => 3,
            'is_active' => '0',
        ]))->assertRedirect(route('admin.welfare-schemes.index'));

        $scheme->refresh();
        $this->assertSame('Updated Welfare Scheme', $scheme->content['en']['name']);
        $this->assertSame(8000, $scheme->monthly_allowance);
        $this->assertSame(3, $scheme->sort_order);
        $this->assertFalse($scheme->is_active);
        $this->assertDatabaseHas('cms_activity_logs', [
            'subject_type' => 'welfare_scheme',
            'subject_id' => $scheme->id,
            'action' => 'updated',
        ]);

        $this->delete(route('admin.welfare-schemes.destroy', $scheme))
            ->assertRedirect(route('admin.welfare-schemes.index'));

        $this->assertDatabaseMissing('welfare_schemes', ['id' => $scheme->id]);
        $this->assertDatabaseHas('cms_activity_logs', [
            'subject_type' => 'welfare_scheme',
            'subject_id' => $scheme->id,
            'action' => 'deleted',
        ]);
    }

    public function test_welfare_scheme_requires_at_least_one_criterion_in_each_language(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'editor']));

        $content = $this->schemeContent();
        $content['si']['indicators'] = " \n ";
        $response = $this->from(route('admin.welfare-schemes.create'))
            ->post(route('admin.welfare-schemes.store'), $this->validSchemeInput(['content' => $content]));

        $response->assertRedirect(route('admin.welfare-schemes.create'))
            ->assertSessionHasErrors('content.si.indicators');
        $this->assertDatabaseCount('welfare_schemes', 5);
    }

    private function validSchemeInput(array $overrides = []): array
    {
        $input = array_merge([
            'content' => $this->schemeContent(),
            'monthly_allowance' => 4000,
            'sort_order' => 1,
            'is_active' => '1',
        ], $overrides);

        foreach (['en', 'si', 'ta'] as $locale) {
            if (is_array($input['content'][$locale]['indicators'])) {
                $input['content'][$locale]['indicators'] = implode("\n", $input['content'][$locale]['indicators']);
            }
        }

        return $input;
    }

    /**
     * @param  array<string, array<string, mixed>>  $overrides
     * @return array<string, array<string, mixed>>
     */
    private function schemeContent(array $overrides = []): array
    {
        $content = [
            'en' => [
                'name' => 'Welfare Scheme',
                'badge' => 'General Support',
                'duration' => 'Continuous',
                'description' => 'Support for eligible households.',
                'indicators' => ['Household income', 'Eligibility verification'],
            ],
            'si' => [
                'name' => 'සුබසාධන යෝජනා ක්‍රමය',
                'badge' => 'සාමාන්‍ය සහාය',
                'duration' => 'අඛණ්ඩව',
                'description' => 'සුදුසුකම් ලත් පවුල් සඳහා සහාය.',
                'indicators' => ['ගෘහස්ථ ආදායම', 'සුදුසුකම් තහවුරු කිරීම'],
            ],
            'ta' => [
                'name' => 'நலன்புரித் திட்டம்',
                'badge' => 'பொது ஆதரவு',
                'duration' => 'தொடர்ச்சியாக',
                'description' => 'தகுதியுள்ள குடும்பங்களுக்கான ஆதரவு.',
                'indicators' => ['குடும்ப வருமானம்', 'தகுதி உறுதிப்படுத்தல்'],
            ],
        ];

        foreach ($overrides as $locale => $fields) {
            $content[$locale] = array_merge($content[$locale], $fields);
        }

        return $content;
    }
}
