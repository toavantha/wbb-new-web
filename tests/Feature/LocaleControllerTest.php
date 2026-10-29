<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LocaleControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_language_switch_persists_and_renders_the_selected_language(): void
    {
        $switchResponse = $this->from('/')->get('/locale/si');

        $switchResponse->assertRedirect('/');
        $switchResponse->assertSessionHas('locale', 'si');

        $pageResponse = $this->get('/');

        $pageResponse->assertOk();
        $pageResponse->assertSee('භාෂාව');
        $pageResponse->assertSee('සුබසාධක ප්‍රතිලාභ මණ්ඩලය');

        $englishSwitchResponse = $this->from('/')->get('/locale/en');

        $englishSwitchResponse->assertRedirect('/');
        $englishSwitchResponse->assertSessionHas('locale', 'en');

        $englishPageResponse = $this->get('/');

        $englishPageResponse->assertOk();
        $englishPageResponse->assertSee('lang="en"', false);
        $englishPageResponse->assertSee('Language:');
    }

    public function test_tamil_language_is_applied_to_public_pages(): void
    {
        $response = $this->withSession(['locale' => 'ta'])->get('/about');

        $response->assertOk();
        $response->assertSee('மொழி');
        $response->assertSee('நலன்புரி நன்மைகள் சபை');
    }

    public function test_unsupported_language_cannot_be_selected(): void
    {
        $response = $this->get('/locale/fr');

        $response->assertNotFound();
    }

    public function test_sinhala_and_tamil_localize_every_public_page_template(): void
    {
        $paths = [
            '/',
            '/about',
            '/schemes',
            '/publications',
            '/downloads',
            '/employees',
            '/media',
            '/careers',
            '/contact',
        ];

        foreach (['si', 'ta'] as $locale) {
            foreach ($paths as $path) {
                $response = $this->withSession(['locale' => $locale])->get($path);

                $response->assertOk();
                $response->assertSee('lang="'.$locale.'"', false);
                $response->assertDontSee('site.');
            }
        }
    }
}
