<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('contact_information')
            ->select(['id', 'latitude', 'longitude'])
            ->orderBy('id')
            ->get()
            ->each(function (object $contactInformation): void {
                DB::table('contact_information')
                    ->where('id', $contactInformation->id)
                    ->update([
                        'map_embed_url' => $this->openStreetMapUrl(
                            (float) $contactInformation->latitude,
                            (float) $contactInformation->longitude,
                        ),
                    ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('contact_information')
            ->select(['id', 'latitude', 'longitude'])
            ->orderBy('id')
            ->get()
            ->each(function (object $contactInformation): void {
                DB::table('contact_information')
                    ->where('id', $contactInformation->id)
                    ->update([
                        'map_embed_url' => sprintf(
                            'https://www.google.com/maps?q=%1$s,%2$s&z=17&output=embed',
                            number_format((float) $contactInformation->latitude, 7, '.', ''),
                            number_format((float) $contactInformation->longitude, 7, '.', ''),
                        ),
                    ]);
            });
    }

    private function openStreetMapUrl(float $latitude, float $longitude): string
    {
        return sprintf(
            'https://www.openstreetmap.org/export/embed.html?bbox=%1$s,%2$s,%3$s,%4$s&layer=mapnik&marker=%5$s,%6$s',
            number_format(max(-180, $longitude - 0.01), 7, '.', ''),
            number_format(max(-90, $latitude - 0.006), 7, '.', ''),
            number_format(min(180, $longitude + 0.01), 7, '.', ''),
            number_format(min(90, $latitude + 0.006), 7, '.', ''),
            number_format($latitude, 7, '.', ''),
            number_format($longitude, 7, '.', ''),
        );
    }
};
