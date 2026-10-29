<?php

namespace Tests\Feature;

use App\Models\NewsItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NewsImageGalleryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_main_and_gallery_images_displayed_on_public_pages(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create());

        $this->get(route('admin.news.create'))
            ->assertOk()
            ->assertSee('name="main_image_si"', false)
            ->assertSee('name="sub_images_ta[]"', false);

        $response = $this->post(route('admin.news.store'), [
            'title' => 'Community outreach programme',
            'category' => 'Announcement',
            'content' => 'The Board held an outreach programme.',
            'published_date' => '2026-10-23',
            'is_published' => '1',
            'main_image' => UploadedFile::fake()->image('main.png'),
            'sub_images' => [
                UploadedFile::fake()->image('gallery-one.jpg'),
                UploadedFile::fake()->image('gallery-two.jpg'),
            ],
        ]);

        $newsItem = NewsItem::query()->where('title', 'Community outreach programme')->firstOrFail();
        $galleryImages = $newsItem->images;

        $response->assertRedirect(route('admin.news.index'));
        $this->assertDatabaseHas('news_items', [
            'id' => $newsItem->id,
            'main_image_path' => $newsItem->main_image_path,
        ]);
        $this->assertCount(2, $galleryImages);
        Storage::disk('public')->assertExists($newsItem->main_image_path);
        Storage::disk('public')->assertExists($galleryImages->pluck('image_path')->all());

        $mainImageUrl = Storage::disk('public')->url($newsItem->main_image_path);
        $this->get(route('home'))
            ->assertOk()
            ->assertSee($mainImageUrl, false);
        $this->get(route('media'))
            ->assertOk()
            ->assertSee($mainImageUrl, false);

        $detailResponse = $this->get(route('media.detail', $newsItem->slug));
        $detailResponse->assertOk()
            ->assertSee($mainImageUrl, false)
            ->assertSee(__('site.media_detail.gallery'));

        foreach ($galleryImages as $galleryImage) {
            $detailResponse->assertSee(Storage::disk('public')->url($galleryImage->image_path), false);
        }
    }

    public function test_public_news_uses_language_specific_images_and_falls_back_to_english(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create());

        $this->post(route('admin.news.store'), [
            'title' => 'Localized image article',
            'category' => 'Announcement',
            'content' => 'Article with language-specific images.',
            'published_date' => '2026-10-23',
            'is_published' => '1',
            'main_image' => UploadedFile::fake()->image('main-en.png'),
            'main_image_si' => UploadedFile::fake()->image('main-si.png'),
            'main_image_ta' => UploadedFile::fake()->image('main-ta.png'),
            'sub_images' => [UploadedFile::fake()->image('gallery-en.png')],
            'sub_images_si' => [UploadedFile::fake()->image('gallery-si.png')],
        ])->assertRedirect(route('admin.news.index'));

        $newsItem = NewsItem::query()->where('title', 'Localized image article')->firstOrFail();
        $galleryImages = $newsItem->images()->get()->keyBy('locale');
        $this->assertDatabaseHas('news_items', [
            'id' => $newsItem->id,
            'main_image_path_si' => $newsItem->main_image_path_si,
            'main_image_path_ta' => $newsItem->main_image_path_ta,
        ]);
        $this->assertSame(['en', 'si'], $galleryImages->pluck('locale')->all());
        Storage::disk('public')->assertExists([
            $newsItem->main_image_path,
            $newsItem->main_image_path_si,
            $newsItem->main_image_path_ta,
            ...$galleryImages->pluck('image_path')->all(),
        ]);

        $englishGalleryUrl = Storage::disk('public')->url($galleryImages['en']->image_path);
        $sinhalaGalleryUrl = Storage::disk('public')->url($galleryImages['si']->image_path);
        $tamilUsesEnglishGalleryUrl = $englishGalleryUrl;
        foreach ([
            'en' => [Storage::disk('public')->url($newsItem->main_image_path), $englishGalleryUrl],
            'si' => [Storage::disk('public')->url($newsItem->main_image_path_si), $sinhalaGalleryUrl],
            'ta' => [Storage::disk('public')->url($newsItem->main_image_path_ta), $tamilUsesEnglishGalleryUrl],
        ] as $locale => [$mainImageUrl, $galleryImageUrl]) {
            $this->withSession(['locale' => $locale])
                ->get(route('media.detail', $newsItem->slug))
                ->assertOk()
                ->assertSee($mainImageUrl, false)
                ->assertSee($galleryImageUrl, false);
        }
    }

    public function test_public_news_falls_back_to_english_images_when_translations_are_missing(): void
    {
        Storage::fake('public');
        $newsItem = $this->createNewsItem();
        $mainImagePath = 'news/'.$newsItem->id.'/main/en.png';
        $galleryImagePath = 'news/'.$newsItem->id.'/gallery/en.png';
        $newsItem->update(['main_image_path' => $mainImagePath]);
        $newsItem->images()->create([
            'image_path' => $galleryImagePath,
            'locale' => 'en',
            'sort_order' => 0,
        ]);
        Storage::disk('public')->put($mainImagePath, 'main');
        Storage::disk('public')->put($galleryImagePath, 'gallery');
        $mainImageUrl = Storage::disk('public')->url($mainImagePath);
        $galleryImageUrl = Storage::disk('public')->url($galleryImagePath);

        foreach (['si', 'ta'] as $locale) {
            $this->withSession(['locale' => $locale])
                ->get(route('media.detail', $newsItem->slug))
                ->assertOk()
                ->assertSee($mainImageUrl, false)
                ->assertSee($galleryImageUrl, false);
        }
    }

    public function test_admin_can_replace_main_image_add_gallery_images_and_remove_selected_images(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create());
        $newsItem = $this->createNewsItem();
        $oldMainImagePath = 'news/'.$newsItem->id.'/main/old.png';
        $oldSinhalaMainImagePath = 'news/'.$newsItem->id.'/main/si/old.png';
        $removedGalleryImage = $newsItem->images()->create([
            'image_path' => 'news/'.$newsItem->id.'/gallery/remove.png',
            'sort_order' => 0,
        ]);
        $retainedGalleryImage = $newsItem->images()->create([
            'image_path' => 'news/'.$newsItem->id.'/gallery/retain.png',
            'sort_order' => 1,
        ]);
        Storage::disk('public')->put($oldMainImagePath, 'old main image');
        Storage::disk('public')->put($removedGalleryImage->image_path, 'remove');
        Storage::disk('public')->put($retainedGalleryImage->image_path, 'retain');
        $newsItem->update([
            'main_image_path' => $oldMainImagePath,
            'main_image_path_si' => $oldSinhalaMainImagePath,
        ]);
        Storage::disk('public')->put($oldSinhalaMainImagePath, 'old Sinhala main image');
        $this->get(route('admin.news.edit', $newsItem))
            ->assertOk()
            ->assertSee('name="main_image_si"', false)
            ->assertSee('name="sub_images_ta[]"', false);

        $response = $this->put(route('admin.news.update', $newsItem), [
            'title' => $newsItem->title,
            'category' => $newsItem->category,
            'content' => $newsItem->content,
            'published_date' => '2026-10-23',
            'remove_images' => [$removedGalleryImage->id],
            'main_image' => UploadedFile::fake()->image('replacement.png'),
            'main_image_si' => UploadedFile::fake()->image('replacement-si.png'),
            'sub_images' => [UploadedFile::fake()->image('added.png')],
        ]);

        $newsItem->refresh();

        $response->assertRedirect(route('admin.news.index'));
        $this->assertNotSame($oldMainImagePath, $newsItem->main_image_path);
        $this->assertNotSame($oldSinhalaMainImagePath, $newsItem->main_image_path_si);
        $this->assertDatabaseMissing('news_images', ['id' => $removedGalleryImage->id]);
        $this->assertDatabaseHas('news_images', ['id' => $retainedGalleryImage->id]);
        $this->assertDatabaseCount('news_images', 2);
        Storage::disk('public')->assertMissing([$oldMainImagePath, $oldSinhalaMainImagePath]);
        Storage::disk('public')->assertMissing($removedGalleryImage->image_path);
        Storage::disk('public')->assertExists($retainedGalleryImage->image_path);
        Storage::disk('public')->assertExists($newsItem->main_image_path);
        Storage::disk('public')->assertExists($newsItem->images()->where('id', '!=', $retainedGalleryImage->id)->value('image_path'));
    }

    public function test_news_images_must_be_valid_image_files(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create());

        $response = $this->from(route('admin.news.create'))->post(route('admin.news.store'), [
            'title' => 'Invalid image article',
            'category' => 'Announcement',
            'content' => 'Article content.',
            'published_date' => '2026-10-23',
            'main_image' => UploadedFile::fake()->create('not-an-image.txt', 10, 'text/plain'),
        ]);

        $response->assertRedirect(route('admin.news.create'))
            ->assertSessionHasErrors('main_image');
        $this->assertDatabaseCount('news_items', 0);
        Storage::disk('public')->assertDirectoryEmpty('news');
    }

    public function test_deleting_news_article_removes_its_main_and_gallery_images(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create());
        $newsItem = $this->createNewsItem();
        $mainImagePath = 'news/'.$newsItem->id.'/main/main.png';
        $sinhalaMainImagePath = 'news/'.$newsItem->id.'/main/si/main.png';
        $tamilMainImagePath = 'news/'.$newsItem->id.'/main/ta/main.png';
        $galleryImagePath = 'news/'.$newsItem->id.'/gallery/gallery.png';
        $sinhalaGalleryImagePath = 'news/'.$newsItem->id.'/gallery/si/gallery.png';
        $newsItem->update([
            'main_image_path' => $mainImagePath,
            'main_image_path_si' => $sinhalaMainImagePath,
            'main_image_path_ta' => $tamilMainImagePath,
        ]);
        $newsItem->images()->create([
            'image_path' => $galleryImagePath,
            'locale' => 'en',
            'sort_order' => 0,
        ]);
        $newsItem->images()->create([
            'image_path' => $sinhalaGalleryImagePath,
            'locale' => 'si',
            'sort_order' => 0,
        ]);
        Storage::disk('public')->put($mainImagePath, 'main');
        Storage::disk('public')->put($sinhalaMainImagePath, 'main si');
        Storage::disk('public')->put($tamilMainImagePath, 'main ta');
        Storage::disk('public')->put($galleryImagePath, 'gallery');
        Storage::disk('public')->put($sinhalaGalleryImagePath, 'gallery si');

        $response = $this->delete(route('admin.news.destroy', $newsItem));

        $response->assertRedirect(route('admin.news.index'));
        $this->assertDatabaseMissing('news_items', ['id' => $newsItem->id]);
        $this->assertDatabaseMissing('news_images', ['news_item_id' => $newsItem->id]);
        Storage::disk('public')->assertMissing([
            $mainImagePath,
            $sinhalaMainImagePath,
            $tamilMainImagePath,
            $galleryImagePath,
            $sinhalaGalleryImagePath,
        ]);
    }

    private function createNewsItem(): NewsItem
    {
        return NewsItem::create([
            'title' => 'News image test article',
            'slug' => 'news-image-test-article',
            'category' => 'Press Release',
            'content' => 'News article content.',
            'published_date' => '2026-10-23',
            'is_published' => true,
            'is_featured' => false,
        ]);
    }
}
