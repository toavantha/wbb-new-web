<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactInformationManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_page_displays_persisted_headquarters_information_and_map(): void
    {
        config(['services.google_maps.key' => 'test-browser-key']);

        $response = $this->get(route('contact.index'));

        $response->assertOk()
            ->assertSee('J.R. Jayawardena Centre,')
            ->assertSee('191 Srimath Anagarika Dharmapala Mawatha,')
            ->assertSee('1924')
            ->assertSee('info@iwms.wbb.gov.lk')
            ->assertSee('id="contact-office-map"', false)
            ->assertSee('data-google-maps-api-key="test-browser-key"', false)
            ->assertDontSee('<script>', false)
            ->assertDontSee('<style>', false)
            ->assertDontSee('onerror=', false)
            ->assertDontSee('<iframe title="Headquarters office location"', false)
            ->assertDontSee('openstreetmap.org/export/embed.html', false)
            ->assertSee('https://www.google.com/maps/search/?api=1&amp;query=6.9160364,79.8586939', false);
        $response->assertSee('Open this location in Google Maps');
    }

    public function test_contact_page_shows_google_maps_link_when_the_maps_api_key_is_missing(): void
    {
        config(['services.google_maps.key' => null]);

        $this->get(route('contact.index'))
            ->assertOk()
            ->assertSee('Google Maps is unavailable right now.')
            ->assertSee('data-google-maps-api-key=""', false)
            ->assertSee('https://www.google.com/maps/search/?api=1&amp;query=6.9160364,79.8586939', false);
    }

    public function test_contact_information_management_is_only_available_to_admins(): void
    {
        $this->get(route('admin.contact-information.edit'))
            ->assertRedirect(route('admin.login'));

        $editor = User::factory()->create([
            'role' => 'editor',
            'is_approved' => true,
        ]);
        $this->actingAs($editor);

        $this->get(route('admin.activity.index'))
            ->assertOk()
            ->assertDontSee('Contact Information');
        $this->get(route('admin.contact-information.edit'))->assertForbidden();
        $this->put(route('admin.contact-information.update'), $this->validContactInformation([
            'address' => 'Unauthorized address',
        ]))->assertForbidden();

        $this->assertDatabaseMissing('contact_information', ['address' => 'Unauthorized address']);
    }

    public function test_admin_can_update_contact_information_and_changes_appear_on_public_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $this->get(route('admin.contact-information.edit'))
            ->assertOk()
            ->assertSee('Headquarters Address')
            ->assertSee('Google Maps Location URL')
            ->assertSee('https://www.google.com/maps/place/Location/@6.9026888,79.9627975,18z/...')
            ->assertDontSee('location-picker')
            ->assertDontSee('Load Location from Link')
            ->assertDontSee('leaflet', false)
            ->assertDontSee('maps.googleapis.com/maps/api/js', false)
            ->assertDontSee('name="latitude"', false)
            ->assertDontSee('name="longitude"', false);

        $this->put(route('admin.contact-information.update'), $this->validContactInformation([
            'address' => "Updated headquarters <script>alert(1)</script>\nNew address line",
            'hotline' => '1919',
            'hotline_hours_en' => 'Monday to Friday, 9:00 AM to 5:00 PM',
            'hotline_hours_si' => 'සතියේ දිනවල පෙ.ව. 9:00 සිට ප.ව. 5:00 දක්වා',
            'hotline_hours_ta' => 'திங்கள் முதல் வெள்ளி வரை காலை 9:00 முதல் மாலை 5:00 வரை',
            'office_phone' => '+94 11 555 1234',
            'email_primary' => 'contact@example.gov.lk',
            'email_secondary' => 'office@example.gov.lk',
            'google_maps_url' => 'https://www.google.com/maps/place/503+Susilarama+Road,+Sri+Jayawardenepura+Kotte/@6.9026888,79.9627975,18z/data=!3m1!4b1!4m5!3m4!1s0x3ae2572a8d19db19:0x2f32affedd6b0d3d!8m2!3d6.9026888!4d79.9627975?entry=ttu&g_ep=EgoyMDI2MTAyMC4wIKXMDSoASAFQAw%3D%3D',
        ]))->assertRedirect(route('admin.contact-information.edit'));

        $this->assertDatabaseHas('contact_information', [
            'id' => 1,
            'address' => "Updated headquarters <script>alert(1)</script>\nNew address line",
            'hotline' => '1919',
            'hotline_hours_en' => 'Monday to Friday, 9:00 AM to 5:00 PM',
            'hotline_hours_si' => 'සතියේ දිනවල පෙ.ව. 9:00 සිට ප.ව. 5:00 දක්වා',
            'hotline_hours_ta' => 'திங்கள் முதல் வெள்ளி வரை காலை 9:00 முதல் மாலை 5:00 வரை',
            'office_phone' => '+94 11 555 1234',
            'email_primary' => 'contact@example.gov.lk',
            'email_secondary' => 'office@example.gov.lk',
            'google_maps_url' => 'https://www.google.com/maps/place/503+Susilarama+Road,+Sri+Jayawardenepura+Kotte/@6.9026888,79.9627975,18z/data=!3m1!4b1!4m5!3m4!1s0x3ae2572a8d19db19:0x2f32affedd6b0d3d!8m2!3d6.9026888!4d79.9627975?entry=ttu&g_ep=EgoyMDI2MTAyMC4wIKXMDSoASAFQAw%3D%3D',
            'latitude' => '6.9026888',
            'longitude' => '79.9627975',
            'map_embed_url' => 'https://www.openstreetmap.org/export/embed.html?bbox=79.9527975,6.8966888,79.9727975,6.9086888&layer=mapnik&marker=6.9026888,79.9627975',
        ]);
        $this->assertDatabaseHas('cms_activity_logs', [
            'user_id' => $admin->id,
            'action' => 'updated',
            'subject_type' => 'contact_information',
            'subject_id' => 1,
        ]);

        config(['services.google_maps.key' => 'test-browser-key']);
        $this->get(route('contact.index'))
            ->assertOk()
            ->assertSee('Updated headquarters')
            ->assertSee('New address line')
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('1919')
            ->assertSee('contact@example.gov.lk')
            ->assertSee('Monday to Friday, 9:00 AM to 5:00 PM')
            ->assertSee('data-google-maps-api-key="test-browser-key"', false)
            ->assertSee('data-latitude="6.9026888"', false)
            ->assertSee('data-longitude="79.9627975"', false)
            ->assertSee('href="https://www.google.com/maps/place/503+Susilarama+Road,+Sri+Jayawardenepura+Kotte/@6.9026888,79.9627975,18z/data=!3m1!4b1!4m5!3m4!1s0x3ae2572a8d19db19:0x2f32affedd6b0d3d!8m2!3d6.9026888!4d79.9627975?entry=ttu&amp;g_ep=EgoyMDI2MTAyMC4wIKXMDSoASAFQAw%3D%3D"', false)
            ->assertSee('Open this location in Google Maps')
            ->assertDontSee('openstreetmap.org/export/embed.html', false);

        $this->withSession(['locale' => 'si'])
            ->get(route('contact.index'))
            ->assertOk()
            ->assertSee('සතියේ දිනවල පෙ.ව. 9:00 සිට ප.ව. 5:00 දක්වා');
    }

    public function test_admin_cannot_save_a_non_google_maps_link(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->from(route('admin.contact-information.edit'))
            ->put(route('admin.contact-information.update'), $this->validContactInformation([
                'google_maps_url' => 'https://attacker.example/maps/redirect',
            ]))
            ->assertRedirect(route('admin.contact-information.edit'))
            ->assertSessionHasErrors('google_maps_url');

        $this->assertDatabaseHas('contact_information', [
            'id' => 1,
            'google_maps_url' => null,
        ]);
    }

    public function test_admin_can_save_a_full_google_maps_place_url_and_update_public_map_coordinates(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->put(route('admin.contact-information.update'), $this->validContactInformation([
            'google_maps_url' => 'https://www.google.com/maps/place/Office/@6.9026888,79.9627975,18z',
        ]))
            ->assertRedirect(route('admin.contact-information.edit'));

        $this->assertDatabaseHas('contact_information', [
            'id' => 1,
            'google_maps_url' => 'https://www.google.com/maps/place/Office/@6.9026888,79.9627975,18z',
            'latitude' => '6.9026888',
            'longitude' => '79.9627975',
        ]);
    }

    public function test_admin_cannot_save_a_google_maps_short_link_without_coordinates(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->from(route('admin.contact-information.edit'))
            ->put(route('admin.contact-information.update'), $this->validContactInformation([
                'google_maps_url' => 'https://maps.app.goo.gl/8Rv2zuXtBkoRMrYQ7',
            ]))
            ->assertRedirect(route('admin.contact-information.edit'))
            ->assertSessionHasErrors('google_maps_url');

        $this->assertDatabaseHas('contact_information', [
            'id' => 1,
            'google_maps_url' => null,
        ]);
    }

    public function test_contact_location_editor_does_not_show_a_map_picker_or_require_a_google_maps_key(): void
    {
        config(['services.google_maps.key' => null]);
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->get(route('admin.contact-information.edit'))
            ->assertOk()
            ->assertSee('Google Maps Location URL')
            ->assertDontSee('location-picker')
            ->assertDontSee('maps.googleapis.com/maps/api/js', false);
    }

    public function test_admin_cannot_save_google_maps_urls_with_out_of_range_coordinates(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->from(route('admin.contact-information.edit'))
            ->put(route('admin.contact-information.update'), $this->validContactInformation([
                'google_maps_url' => 'https://www.google.com/maps/place/Office/@91,181,18z',
            ]))
            ->assertRedirect(route('admin.contact-information.edit'))
            ->assertSessionHasErrors('google_maps_url');

        $this->assertDatabaseHas('contact_information', [
            'id' => 1,
            'latitude' => 6.9160364,
            'longitude' => 79.8586939,
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function validContactInformation(array $overrides = []): array
    {
        return array_merge([
            'address' => 'J.R. Jayawardena Centre',
            'hotline' => '1924',
            'hotline_hours_en' => 'Operates weekdays 8:30 AM – 4:30 PM',
            'hotline_hours_si' => 'සතියේ දිනවල පෙ.ව. 8:30 – ප.ව. 4:30 දක්වා ක්‍රියාත්මක වේ',
            'hotline_hours_ta' => 'வார நாட்களில் காலை 8:30 – மாலை 4:30 வரை செயற்படும்',
            'office_phone' => '+94 11 267 2252 / +94 11 267 2253',
            'email_primary' => 'info@iwms.wbb.gov.lk',
            'email_secondary' => 'commissioner.wbb@mo.treasury.gov.lk',
            'latitude' => 6.9160364,
            'longitude' => 79.8586939,
        ], $overrides);
    }
}
