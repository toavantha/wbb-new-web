<?php

namespace Tests\Feature;

use App\Models\NewsItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsLocalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_cms_users_can_publish_news_content_in_english_sinhala_and_tamil(): void
    {
        $this->actingAs(User::factory()->create());

        $response = $this->post(route('admin.news.store'), [
            'title' => 'English news title',
            'title_si' => 'සිංහල පුවත් මාතෘකාව',
            'title_ta' => 'தமிழ் செய்தித் தலைப்பு',
            'category' => 'Announcement',
            'excerpt' => 'English summary',
            'excerpt_si' => 'සිංහල සාරාංශය',
            'excerpt_ta' => 'தமிழ் சுருக்கம்',
            'content' => 'English article content',
            'content_si' => 'සිංහල ලිපි අන්තර්ගතය',
            'content_ta' => 'தமிழ் கட்டுரை உள்ளடக்கம்',
            'published_date' => '2026-10-25',
            'is_published' => '1',
            'is_featured' => '1',
        ]);

        $newsItem = NewsItem::query()->where('title', 'English news title')->firstOrFail();
        $response->assertRedirect(route('admin.news.index'));
        $this->assertDatabaseHas('news_items', [
            'id' => $newsItem->id,
            'title_si' => 'සිංහල පුවත් මාතෘකාව',
            'title_ta' => 'தமிழ் செய்தித் தலைப்பு',
            'excerpt_si' => 'සිංහල සාරාංශය',
            'excerpt_ta' => 'தமிழ் சுருக்கம்',
            'content_si' => 'සිංහල ලිපි අන්තර්ගතය',
            'content_ta' => 'தமிழ் கட்டுரை உள்ளடக்கம்',
        ]);

        foreach ([
            'en' => ['English news title', 'English summary', 'English article content'],
            'si' => ['සිංහල පුවත් මාතෘකාව', 'සිංහල සාරාංශය', 'සිංහල ලිපි අන්තර්ගතය'],
            'ta' => ['தமிழ் செய்தித் தலைப்பு', 'தமிழ் சுருக்கம்', 'தமிழ் கட்டுரை உள்ளடக்கம்'],
        ] as $locale => [$title, $excerpt, $content]) {
            $this->withSession(['locale' => $locale])
                ->get(route('media'))
                ->assertOk()
                ->assertSee($title)
                ->assertSee($excerpt);

            $this->withSession(['locale' => $locale])
                ->get(route('media.detail', $newsItem->slug))
                ->assertOk()
                ->assertSee($title)
                ->assertSee($excerpt)
                ->assertSee($content);

            $this->withSession(['locale' => $locale])
                ->get(route('home'))
                ->assertOk()
                ->assertSee($title)
                ->assertSee($excerpt);
        }
    }

    public function test_missing_sinhala_or_tamil_content_falls_back_to_english(): void
    {
        $newsItem = NewsItem::create([
            'title' => 'English fallback title',
            'title_si' => 'සිංහල පරිවර්තනය',
            'category' => 'Announcement',
            'excerpt' => 'English fallback summary',
            'content' => 'English fallback body',
            'content_si' => 'සිංහල අන්තර්ගතය',
            'slug' => 'english-fallback-title',
            'published_date' => '2026-10-25',
            'is_published' => true,
        ]);

        $this->withSession(['locale' => 'si'])
            ->get(route('media.detail', $newsItem->slug))
            ->assertOk()
            ->assertSee('සිංහල පරිවර්තනය')
            ->assertSee('English fallback summary')
            ->assertSee('සිංහල අන්තර්ගතය');

        $this->withSession(['locale' => 'ta'])
            ->get(route('media.detail', $newsItem->slug))
            ->assertOk()
            ->assertSee('English fallback title')
            ->assertSee('English fallback summary')
            ->assertSee('English fallback body');
    }

    public function test_news_translations_are_escaped_when_rendered(): void
    {
        $newsItem = NewsItem::create([
            'title' => 'English title',
            'title_ta' => '<script>alert("xss")</script>',
            'category' => 'Announcement',
            'content' => 'English content',
            'slug' => 'escaped-news-translation',
            'published_date' => '2026-10-25',
            'is_published' => true,
        ]);

        $this->withSession(['locale' => 'ta'])
            ->get(route('media.detail', $newsItem->slug))
            ->assertOk()
            ->assertSee('&lt;script&gt;', false)
            ->assertDontSee('<script>alert("xss")</script>', false);
    }
}
