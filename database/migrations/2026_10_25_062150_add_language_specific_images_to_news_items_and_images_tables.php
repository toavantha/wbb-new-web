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
        Schema::table('news_items', function (Blueprint $table) {
            $table->string('main_image_path_si')->nullable()->after('main_image_path');
            $table->string('main_image_path_ta')->nullable()->after('main_image_path_si');
        });

        Schema::table('news_images', function (Blueprint $table) {
            $table->string('locale', 2)->default('en')->after('image_path');
            $table->index(['news_item_id', 'locale', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news_items', function (Blueprint $table) {
            $table->dropColumn(['main_image_path_si', 'main_image_path_ta']);
        });

        Schema::table('news_images', function (Blueprint $table) {
            $table->dropIndex(['news_item_id', 'locale', 'sort_order']);
            $table->dropColumn('locale');
        });
    }
};
