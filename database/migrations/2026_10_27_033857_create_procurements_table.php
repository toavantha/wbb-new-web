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
        Schema::create('procurements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_si')->nullable();
            $table->string('title_ta')->nullable();
            $table->string('reference_no')->nullable();
            $table->string('category'); // Procurement Notice, Tender Document, TOR, Other
            $table->text('description')->nullable();
            $table->text('description_si')->nullable();
            $table->text('description_ta')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_path_si')->nullable();
            $table->string('file_path_ta')->nullable();
            $table->string('file_size')->nullable();
            $table->string('file_size_si')->nullable();
            $table->string('file_size_ta')->nullable();
            $table->date('published_date');
            $table->date('deadline_date');
            $table->date('original_deadline_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procurements');
    }
};
