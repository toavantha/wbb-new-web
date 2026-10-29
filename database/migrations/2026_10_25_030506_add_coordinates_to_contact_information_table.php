<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contact_information', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->default(6.9160364);
            $table->decimal('longitude', 10, 7)->default(79.8586939);
        });

        DB::table('contact_information')
            ->where('id', 1)
            ->update([
                'map_embed_url' => 'https://www.openstreetmap.org/export/embed.html?bbox=79.8486939,6.9100364,79.8686939,6.9220364&layer=mapnik&marker=6.9160364,79.8586939',
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_information', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};
