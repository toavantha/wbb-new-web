<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactInformation;
use App\Services\CmsActivityLogger;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactInformationController extends Controller
{
    public function edit(): View
    {
        $contactInformation = ContactInformation::query()->findOrFail(1);

        return view('admin.contact-information.edit', compact('contactInformation'));
    }

    public function update(Request $request, CmsActivityLogger $activityLogger): RedirectResponse
    {
        $contactInformation = ContactInformation::query()->findOrFail(1);

        $validated = $request->validate([
            'address' => ['required', 'string', 'max:2000'],
            'hotline' => ['required', 'string', 'max:50', 'regex:/\A[0-9+ ()\/-]+\z/'],
            'hotline_hours_en' => ['required', 'string', 'max:191'],
            'hotline_hours_si' => ['required', 'string', 'max:191'],
            'hotline_hours_ta' => ['required', 'string', 'max:191'],
            'office_phone' => ['required', 'string', 'max:100', 'regex:/\A[0-9+ ()\/-]+\z/'],
            'email_primary' => ['required', 'email', 'max:191'],
            'email_secondary' => ['required', 'email', 'max:191'],
            'google_maps_url' => ['nullable', ...$this->googleMapsUrlRules()],
        ]);
        $coordinates = filled($validated['google_maps_url'] ?? null)
            ? $this->coordinatesFromGoogleMapsUrl($validated['google_maps_url'])
            : null;

        if ($coordinates !== null) {
            $validated['latitude'] = $coordinates['latitude'];
            $validated['longitude'] = $coordinates['longitude'];
        }

        $latitude = (float) ($validated['latitude'] ?? $contactInformation->latitude);
        $longitude = (float) ($validated['longitude'] ?? $contactInformation->longitude);
        $validated['map_embed_url'] = sprintf(
            'https://www.openstreetmap.org/export/embed.html?bbox=%1$s,%2$s,%3$s,%4$s&layer=mapnik&marker=%5$s,%6$s',
            number_format(max(-180, $longitude - 0.01), 7, '.', ''),
            number_format(max(-90, $latitude - 0.006), 7, '.', ''),
            number_format(min(180, $longitude + 0.01), 7, '.', ''),
            number_format(min(90, $latitude + 0.006), 7, '.', ''),
            number_format($latitude, 7, '.', ''),
            number_format($longitude, 7, '.', ''),
        );

        $contactInformation->update($validated);
        $activityLogger->record(
            $request,
            'updated',
            'contact_information',
            $contactInformation->id,
            'Contact page headquarters information',
        );

        return redirect()->route('admin.contact-information.edit')
            ->with('success', 'Contact page information updated successfully.');
    }

    /**
     * @return array<int, mixed>
     */
    private function googleMapsUrlRules(): array
    {
        return [
            'string',
            'url',
            'max:2048',
            function (string $attribute, mixed $value, Closure $fail): void {
                $coordinates = is_string($value) ? $this->coordinatesFromGoogleMapsUrl($value) : null;
                if ($coordinates === null) {
                    $fail('Enter a full Google Maps location URL containing coordinates, such as google.com/maps/place/.../@latitude,longitude.');
                }
            },
        ];
    }

    /**
     * @return array{latitude: float, longitude: float}|null
     */
    private function coordinatesFromGoogleMapsUrl(string $url): ?array
    {
        $parts = parse_url($url);
        if (! is_array($parts)
            || ($parts['scheme'] ?? '') !== 'https'
            || ! in_array(strtolower($parts['host'] ?? ''), ['google.com', 'www.google.com', 'maps.google.com'], true)
            || preg_match('#^/maps/place(?:/|$)#', $parts['path'] ?? '') !== 1) {
            return null;
        }

        $decodedPath = urldecode($parts['path'] ?? '');
        $patterns = [
            '/!3d(-?\d+(?:\.\d+)?)!4d(-?\d+(?:\.\d+)?)/',
            '/@(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)(?:,|\/|$)/',
        ];
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $decodedPath, $matches) === 1) {
                $latitude = (float) $matches[1];
                $longitude = (float) $matches[2];
                if ($latitude >= -90 && $latitude <= 90 && $longitude >= -180 && $longitude <= 180) {
                    return ['latitude' => $latitude, 'longitude' => $longitude];
                }
            }
        }

        return null;
    }
}
