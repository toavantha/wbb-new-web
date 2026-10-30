<?php

namespace Tests\Feature;

use App\Models\ImportantNotice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportantNoticeManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_displays_active_notices_in_the_selected_language_and_order(): void
    {
        $firstNotice = ImportantNotice::create([
            'message_en' => 'First English notice',
            'message_si' => 'පළමු සිංහල දැනුම්දීම',
            'message_ta' => 'முதல் தமிழ் அறிவிப்பு',
            'sort_order' => 2,
            'is_active' => true,
        ]);
        $secondNotice = ImportantNotice::create([
            'message_en' => 'Second English notice',
            'message_si' => 'දෙවන සිංහල දැනුම්දීම',
            'message_ta' => 'இரண்டாவது தமிழ் அறிவிப்பு',
            'sort_order' => 5,
            'is_active' => true,
        ]);
        ImportantNotice::create([
            'message_en' => 'Escaped English notice',
            'message_si' => 'HTML ටැග් සහිත සිංහල දැනුම්දීම',
            'message_ta' => '<script>alert("notice")</script>',
            'sort_order' => 6,
            'is_active' => true,
        ]);
        ImportantNotice::create([
            'message_en' => 'Inactive notice',
            'message_si' => 'අක්‍රිය දැනුම්දීම',
            'message_ta' => 'செயலற்ற அறிவிப்பு',
            'sort_order' => 0,
            'is_active' => false,
        ]);

        foreach ([
            'en' => [$firstNotice->message_en, $secondNotice->message_en],
            'si' => [$firstNotice->message_si, $secondNotice->message_si],
            'ta' => [$firstNotice->message_ta, $secondNotice->message_ta],
        ] as $locale => [$firstMessage, $secondMessage]) {
            $response = $this->withSession(['locale' => $locale])->get(route('home'));

            $response->assertOk()
                ->assertSee('important-information')
                ->assertSee('data-important-information-toggle', false)
                ->assertSee($firstMessage)
                ->assertSee($secondMessage)
                ->assertDontSee('Inactive notice');

            $homeHtml = $response->getContent();
            $tickerPosition = strpos($homeHtml, 'data-important-information-track');
            $tickerHtml = substr($homeHtml, $tickerPosition);

            $this->assertLessThan(
                strpos($tickerHtml, $secondMessage),
                strpos($tickerHtml, $firstMessage),
            );

            $this->assertLessThan(
                strpos($homeHtml, 'id="important-information"'),
                strpos($homeHtml, '</section>'),
            );
            $this->assertLessThan(
                strpos($homeHtml, '<!-- National Key Figures -->'),
                strpos($homeHtml, 'id="important-information"'),
            );
        }

        $this->withSession(['locale' => 'ta'])
            ->get(route('home'))
            ->assertSee('&lt;script&gt;', false)
            ->assertDontSee('<script>alert("notice")</script>', false);
    }

    public function test_cms_users_can_create_update_and_delete_trilingual_notices(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->get(route('admin.important-information.create'))
            ->assertOk()
            ->assertSee('message_en')
            ->assertSee('message_si')
            ->assertSee('message_ta');

        $response = $this->post(
            route('admin.important-information.store'),
            $this->validNoticeInput(['message_en' => 'New English notice']),
        );
        $notice = ImportantNotice::query()->where('message_en', 'New English notice')->firstOrFail();

        $response->assertRedirect(route('admin.important-information.index'));
        $this->assertDatabaseHas('important_notices', [
            'id' => $notice->id,
            'message_en' => 'New English notice',
            'message_si' => 'නව සිංහල දැනුම්දීම',
            'message_ta' => 'புதிய தமிழ் அறிவிப்பு',
            'sort_order' => 3,
            'is_active' => true,
        ]);
        $this->assertDatabaseHas('cms_activity_logs', [
            'subject_type' => 'important_notice',
            'subject_id' => $notice->id,
            'action' => 'created',
        ]);

        $this->put(
            route('admin.important-information.update', $notice),
            $this->validNoticeInput([
                'message_en' => 'Updated English notice',
                'sort_order' => 1,
                'is_active' => '0',
            ]),
        )->assertRedirect(route('admin.important-information.index'));

        $notice->refresh();
        $this->assertSame('Updated English notice', $notice->message_en);
        $this->assertSame(1, $notice->sort_order);
        $this->assertFalse($notice->is_active);
        $this->assertDatabaseHas('cms_activity_logs', [
            'subject_type' => 'important_notice',
            'subject_id' => $notice->id,
            'action' => 'updated',
        ]);

        $this->delete(route('admin.important-information.destroy', $notice))
            ->assertRedirect(route('admin.important-information.index'));

        $this->assertDatabaseMissing('important_notices', ['id' => $notice->id]);
        $this->assertDatabaseHas('cms_activity_logs', [
            'subject_type' => 'important_notice',
            'subject_id' => $notice->id,
            'action' => 'deleted',
        ]);
    }

    public function test_cms_management_requires_authentication_and_all_three_translations(): void
    {
        $this->get(route('admin.important-information.index'))
            ->assertRedirect(route('admin.login'));

        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->from(route('admin.important-information.create'))
            ->post(
                route('admin.important-information.store'),
                $this->validNoticeInput(['message_si' => '']),
            )
            ->assertRedirect(route('admin.important-information.create'))
            ->assertSessionHasErrors('message_si');

        $this->assertDatabaseCount('important_notices', 0);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validNoticeInput(array $overrides = []): array
    {
        return array_merge([
            'message_en' => 'English important notice',
            'message_si' => 'නව සිංහල දැනුම්දීම',
            'message_ta' => 'புதிய தமிழ் அறிவிப்பு',
            'sort_order' => 3,
            'is_active' => '1',
        ], $overrides);
    }
}
