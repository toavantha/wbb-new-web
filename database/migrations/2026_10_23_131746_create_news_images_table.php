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
            $table->string('main_image_path')->nullable()->after('image_url');
        });

        Schema::create('news_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_item_id')->constrained()->cascadeOnDelete();
            $table->string('image_path');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news_images');

        Schema::table('news_items', function (Blueprint $table) {
            $table->dropColumn('main_image_path');
        });
    }
};
