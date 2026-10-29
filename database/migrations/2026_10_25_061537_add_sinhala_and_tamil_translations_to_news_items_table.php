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
            $table->string('title_si')->nullable();
            $table->text('excerpt_si')->nullable();
            $table->longText('content_si')->nullable();
            $table->string('title_ta')->nullable();
            $table->text('excerpt_ta')->nullable();
            $table->longText('content_ta')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news_items', function (Blueprint $table) {
            $table->dropColumn([
                'title_si',
                'excerpt_si',
                'content_si',
                'title_ta',
                'excerpt_ta',
                'content_ta',
            ]);
        });
    }
};
