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
        Schema::table('careers', function (Blueprint $table) {
            $table->string('tor_si_file_path')->nullable();
            $table->string('details_si_file_path')->nullable();
            $table->string('application_si_file_path')->nullable();
            $table->string('tor_ta_file_path')->nullable();
            $table->string('details_ta_file_path')->nullable();
            $table->string('application_ta_file_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('careers', function (Blueprint $table) {
            $table->dropColumn([
                'tor_si_file_path',
                'details_si_file_path',
                'application_si_file_path',
                'tor_ta_file_path',
                'details_ta_file_path',
                'application_ta_file_path',
            ]);
        });
    }
};
