<?php

namespace App\Services;

use App\Models\BoardMember;
use App\Models\Career;
use App\Models\ContactInformation;
use App\Models\Employee;
use App\Models\GovernmentAffiliation;
use App\Models\NewsItem;
use App\Models\Procurement;
use App\Models\Publication;
use App\Models\WelfareScheme;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class GlobalSearch
{
    private const RESULTS_PER_TYPE = 10;

    /**
     * @return array<int, array{type: string, title: string, excerpt: string, url: string, locale: string|null}>
     */
    public function search(string $term): array
    {
        $results = [];
        $escapedTerm = str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $term);
        $pattern = '%'.$escapedTerm.'%';

        $newsItems = NewsItem::query()
            ->where('is_published', true)
            ->where(function (Builder $query) use ($pattern): void {
                $this->whereLikeAny($query, [
                    'title', 'title_si', 'title_ta',
                    'excerpt', 'excerpt_si', 'excerpt_ta',
                    'content', 'content_si', 'content_ta', 'category',
                ], $pattern);
            })
            ->orderByDesc('published_date')
            ->orderByDesc('id')
            ->limit(self::RESULTS_PER_TYPE)
            ->get();

        foreach ($newsItems as $newsItem) {
            $title = $this->matchingValue([
                $newsItem->title,
                $newsItem->title_si,
                $newsItem->title_ta,
            ], $term) ?? $newsItem->localized('title') ?? '';
            $excerpt = $this->matchingValue([
                $newsItem->excerpt,
                $newsItem->excerpt_si,
                $newsItem->excerpt_ta,
                $newsItem->content,
                $newsItem->content_si,
                $newsItem->content_ta,
                $newsItem->category,
            ], $term) ?? '';

            $results[] = $this->result(
                'news',
                $title,
                $excerpt,
                route('media.detail', $newsItem->slug)
            );
        }

        $employees = Employee::query()
            ->where('is_active', true)
            ->where(function (Builder $query) use ($pattern): void {
                $this->whereLikeAny($query, [
                    'name', 'name_si', 'name_ta',
                    'designation', 'designation_si', 'designation_ta', 'mobile', 'email',
                ], $pattern);
            })
            ->orderBy('name')
            ->orderBy('id')
            ->limit(self::RESULTS_PER_TYPE)
            ->get();

        foreach ($employees as $employee) {
            $names = [$employee->name, $employee->name_si, $employee->name_ta];
            $employeeDetails = [
                $employee->designation,
                $employee->designation_si,
                $employee->designation_ta,
                $employee->mobile,
                $employee->email,
            ];

            $results[] = $this->result(
                'employee',
                $this->matchingValue($names, $term) ?? $employee->localizedName(),
                $this->matchingValue($employeeDetails, $term) ?? '',
                route('employees')
            );
        }

        $schemes = WelfareScheme::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->limit(50)
            ->get();

        foreach ($schemes as $scheme) {
            foreach ($scheme->content ?? [] as $locale => $localizedContent) {
                if (! is_array($localizedContent)) {
                    continue;
                }

                $schemeContent = [
                    ...$this->flattenStrings($localizedContent),
                    (string) $scheme->monthly_allowance,
                ];
                $matchedText = $this->matchingValue($schemeContent, $term);

                if ($matchedText === null) {
                    continue;
                }

                $results[] = $this->result(
                    'scheme',
                    $localizedContent['name'] ?? $matchedText,
                    $matchedText,
                    route('schemes'),
                    in_array($locale, ['en', 'si', 'ta'], true) ? $locale : null
                );

                if ($this->countType($results, 'scheme') >= self::RESULTS_PER_TYPE) {
                    break 2;
                }
            }
        }

        $publications = Publication::query()
            ->where('is_active', true)
            ->where(function (Builder $query) use ($pattern): void {
                $this->whereLikeAny($query, [
                    'title', 'category', 'document_number', 'language', 'description',
                ], $pattern);
            })
            ->orderByDesc('published_date')
            ->orderByDesc('id')
            ->limit(self::RESULTS_PER_TYPE)
            ->get();

        foreach ($publications as $publication) {
            $results[] = $this->result(
                'publication',
                $this->matchingValue([$publication->title], $term) ?? $publication->title,
                $this->matchingValue([
                    $publication->description,
                    $publication->document_number,
                    $publication->category,
                    $publication->language,
                    $publication->published_date?->format('Y-m-d'),
                ], $term) ?? '',
                route('publications', ['search' => $term])
            );
        }

        $procurements = Procurement::query()
            ->where('is_active', true)
            ->where(function (Builder $query) use ($pattern): void {
                $this->whereLikeAny($query, [
                    'title', 'title_si', 'title_ta', 'reference_no',
                    'description', 'description_si', 'description_ta', 'category',
                ], $pattern);
            })
            ->orderByDesc('published_date')
            ->orderByDesc('id')
            ->limit(self::RESULTS_PER_TYPE)
            ->get();

        foreach ($procurements as $procurement) {
            $results[] = $this->result(
                'procurement',
                $this->matchingValue([
                    $procurement->title,
                    $procurement->title_si,
                    $procurement->title_ta,
                ], $term) ?? $procurement->localizedTitle(),
                $this->matchingValue([
                    $procurement->description,
                    $procurement->description_si,
                    $procurement->description_ta,
                    $procurement->reference_no,
                    $procurement->category,
                ], $term) ?? '',
                route('procurements', [
                    'status' => $procurement->isExpired() ? 'expired' : 'current',
                    'search' => $term,
                ])
            );
        }

        $careers = Career::query()
            ->where('is_active', true)
            ->where(function (Builder $query) use ($pattern): void {
                $this->whereLikeAny($query, [
                    'title', 'department', 'employment_type', 'location',
                    'salary_scale', 'description', 'qualifications', 'closing_date',
                ], $pattern);
            })
            ->orderBy('closing_date')
            ->orderBy('id')
            ->limit(self::RESULTS_PER_TYPE)
            ->get();

        foreach ($careers as $career) {
            $results[] = $this->result(
                'career',
                $this->matchingValue([$career->title], $term) ?? $career->title,
                $this->matchingValue([
                    $career->department,
                    $career->employment_type,
                    $career->location,
                    $career->salary_scale,
                    $career->description,
                    $career->qualifications,
                    $career->closing_date?->format('Y-m-d'),
                ], $term) ?? '',
                route('careers')
            );
        }

        $boardMembers = BoardMember::query()
            ->where('is_active', true)
            ->where(function (Builder $query) use ($pattern): void {
                $this->whereLikeAny($query, [
                    'name', 'name_si', 'name_ta', 'position', 'email',
                ], $pattern);
            })
            ->orderBy('sort_order')
            ->orderBy('id')
            ->limit(self::RESULTS_PER_TYPE)
            ->get();

        foreach ($boardMembers as $boardMember) {
            $results[] = $this->result(
                'board_member',
                $this->matchingValue([
                    $boardMember->name,
                    $boardMember->name_si,
                    $boardMember->name_ta,
                ], $term) ?? $boardMember->localizedName(),
                $this->matchingValue([$boardMember->position, $boardMember->email], $term) ?? '',
                route('about')
            );
        }

        $affiliations = GovernmentAffiliation::query()
            ->where('is_active', true)
            ->where(function (Builder $query) use ($pattern): void {
                $this->whereLikeAny($query, [
                    'name_en', 'name_si', 'name_ta',
                    'title_en', 'title_si', 'title_ta',
                    'ministry_en', 'ministry_si', 'ministry_ta',
                ], $pattern);
            })
            ->orderBy('sort_order')
            ->orderBy('id')
            ->limit(self::RESULTS_PER_TYPE)
            ->get();

        foreach ($affiliations as $affiliation) {
            $results[] = $this->result(
                'affiliation',
                $this->matchingValue([
                    $affiliation->name_en,
                    $affiliation->name_si,
                    $affiliation->name_ta,
                    $affiliation->title_en,
                    $affiliation->title_si,
                    $affiliation->title_ta,
                ], $term) ?? $affiliation->localizedName(),
                $this->matchingValue([
                    $affiliation->ministry_en,
                    $affiliation->ministry_si,
                    $affiliation->ministry_ta,
                ], $term) ?? $affiliation->localizedTitle(),
                route('about')
            );
        }

        $contactInformation = ContactInformation::query()->limit(1)->get();

        foreach ($contactInformation as $contact) {
            $contactFields = [
                $contact->address,
                $contact->hotline,
                $contact->hotline_hours_en,
                $contact->hotline_hours_si,
                $contact->hotline_hours_ta,
                $contact->office_phone,
                $contact->email_primary,
                $contact->email_secondary,
            ];

            if (! $this->containsTerm($contactFields, $term)) {
                continue;
            }

            $results[] = $this->result(
                'contact',
                __('site.search.types.contact'),
                $this->matchingValue($contactFields, $term) ?? '',
                route('contact.index')
            );
        }

        return array_merge($results, $this->searchStaticText($term));
    }

    /**
     * @param  array<int, string|null>  $fields
     */
    private function whereLikeAny(Builder $query, array $fields, string $pattern): void
    {
        foreach ($fields as $index => $field) {
            $condition = $query->getQuery()->getGrammar()->wrap($field)." LIKE ? ESCAPE '!'";

            if ($index === 0) {
                $query->whereRaw($condition, [$pattern]);
            } else {
                $query->orWhereRaw($condition, [$pattern]);
            }
        }
    }

    /**
     * @param  array<int, mixed>  $values
     */
    private function matchingValue(array $values, string $term): ?string
    {
        $normalizedTerm = Str::lower($term);

        foreach ($values as $value) {
            if (is_string($value) && Str::contains(Str::lower($value), $normalizedTerm)) {
                return $value;
            }
        }

        return null;
    }

    /**
     * @param  array<int, mixed>  $values
     */
    private function containsTerm(array $values, string $term): bool
    {
        return $this->matchingValue($values, $term) !== null;
    }

    /**
     * @param  array<int, array{type: string, title: string, excerpt: string, url: string, locale: string|null}>  $results
     */
    private function countType(array $results, string $type): int
    {
        return count(array_filter(
            $results,
            fn (array $result): bool => $result['type'] === $type
        ));
    }

    /**
     * @param  array<int|string, mixed>  $values
     * @return array<int, string>
     */
    private function flattenStrings(array $values): array
    {
        $strings = [];

        foreach ($values as $value) {
            if (is_array($value)) {
                $strings = array_merge($strings, $this->flattenStrings($value));
            } elseif (is_string($value) && trim($value) !== '') {
                $strings[] = $value;
            }
        }

        return $strings;
    }

    /**
     * @return array<int, array{type: string, title: string, excerpt: string, url: string, locale: string|null}>
     */
    private function searchStaticText(string $term): array
    {
        $results = [];

        foreach (['en', 'si', 'ta'] as $locale) {
            $translations = require lang_path($locale.'/site.php');
            $localeResults = 0;

            foreach ($this->flattenTranslationEntries($translations) as $key => $text) {
                if (! $this->containsTerm([$text], $term)) {
                    continue;
                }

                $results[] = $this->result(
                    'page',
                    $this->staticPageTitle($this->staticPageKey($key), $locale),
                    Str::limit($text, 240),
                    $this->staticPageUrl($this->staticPageKey($key)),
                    $locale
                );
                $localeResults++;

                if ($localeResults >= self::RESULTS_PER_TYPE) {
                    break;
                }
            }
        }

        return $results;
    }

    private function staticPageTitle(string $pageKey, string $locale): string
    {
        $titleKey = match ($pageKey) {
            'search' => null,
            'about' => 'about',
            'schemes' => 'schemes',
            'publications' => 'publications',
            'downloads' => 'downloads',
            'employees' => 'employees',
            'media' => 'media',
            'careers' => 'careers',
            'procurements' => 'procurements',
            'contact' => 'contact',
            default => 'home',
        };

        return $titleKey === null
            ? trans('site.search.title', [], $locale)
            : trans('site.titles.'.$titleKey, [], $locale);
    }

    private function staticPageUrl(string $pageKey): string
    {
        return match ($pageKey) {
            'about' => route('about'),
            'search' => route('search'),
            'schemes' => route('schemes'),
            'publications' => route('publications'),
            'downloads' => route('downloads'),
            'employees' => route('employees'),
            'media' => route('media'),
            'careers' => route('careers'),
            'procurements' => route('procurements'),
            'contact' => route('contact.index'),
            default => route('home'),
        };
    }

    /**
     * @param  array<string, mixed>  $translations
     */
    private function staticPageKey(string $key): string
    {
        $segments = explode('.', $key);
        $section = $segments[0];
        $pageKey = in_array($section, ['nav', 'titles'], true)
            ? ($segments[1] ?? 'home')
            : $section;

        if ($section === 'footer') {
            return match ($segments[1] ?? '') {
                'about_board', 'organization', 'established' => 'about',
                'welfare_tiers' => 'schemes',
                'acts_gazettes' => 'publications',
                'application_downloads' => 'downloads',
                'need_assistance', 'hotline_hours', 'dial', 'send_message',
                'headquarters', 'view_map', 'general_phone' => 'contact',
                default => 'home',
            };
        }

        return match ($pageKey) {
            'about', 'employees', 'publications', 'downloads', 'media',
            'careers', 'procurements', 'contact', 'schemes' => $pageKey,
            'about_people' => 'about',
            'media_detail' => 'media',
            'hotline' => 'contact',
            'scheme_data' => 'schemes',
            'contact_us' => 'contact',
            'downloads_forms' => 'downloads',
            'media_press' => 'media',
            'search' => 'search',
            default => 'home',
        };
    }

    /**
     * @param  array<int|string, mixed>  $values
     * @return array<string, string>
     */
    private function flattenTranslationEntries(array $values, string $prefix = ''): array
    {
        $entries = [];

        foreach ($values as $key => $value) {
            $translationKey = $prefix === '' ? (string) $key : $prefix.'.'.$key;

            if ($translationKey === 'search' || str_starts_with($translationKey, 'search.')) {
                continue;
            }

            if (is_array($value)) {
                $entries = array_merge(
                    $entries,
                    $this->flattenTranslationEntries($value, $translationKey)
                );
            } elseif (is_string($value) && trim($value) !== '') {
                $entries[$translationKey] = $value;
            }
        }

        return $entries;
    }

    /**
     * @return array{type: string, title: string, excerpt: string, url: string, locale: string|null}
     */
    private function result(
        string $type,
        string $title,
        string $excerpt,
        string $url,
        ?string $locale = null
    ): array {
        return [
            'type' => $type,
            'title' => Str::limit($title, 140),
            'excerpt' => Str::limit($excerpt, 240),
            'url' => $url,
            'locale' => $locale,
        ];
    }
}
