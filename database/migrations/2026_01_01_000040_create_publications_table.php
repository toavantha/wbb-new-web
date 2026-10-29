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
        Schema::create('publications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category'); // Act, Gazette, Circular, Handbook, Application Form
            $table->string('document_number')->nullable();
            $table->string('language')->default('All'); // English, Sinhala, Tamil, Trilingual
            $table->text('description')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_url')->nullable();
            $table->string('file_size')->nullable();
            $table->date('published_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publications');
    }
};
