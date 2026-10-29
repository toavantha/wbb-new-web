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
        Schema::create('careers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('department')->default('Operations');
            $table->string('employment_type')->default('Full-Time'); // Full-Time, Contract, Consultant
            $table->string('location')->default('Head Office, Colombo');
            $table->string('salary_scale')->nullable();
            $table->text('description');
            $table->text('qualifications');
            $table->date('closing_date');
            $table->string('attachment_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('careers');
    }
};
