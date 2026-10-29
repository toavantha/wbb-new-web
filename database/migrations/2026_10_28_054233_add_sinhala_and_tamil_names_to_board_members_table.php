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
        Schema::table('board_members', function (Blueprint $table) {
            $table->string('name_si')->nullable();
            $table->string('name_ta')->nullable();
        });

        DB::table('board_members')->whereNull('name_si')->update(['name_si' => DB::raw('name')]);
        DB::table('board_members')->whereNull('name_ta')->update(['name_ta' => DB::raw('name')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('board_members', function (Blueprint $table) {
            $table->dropColumn(['name_si', 'name_ta']);
        });
    }
};
