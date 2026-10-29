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
        Schema::table('employees', function (Blueprint $table) {
            $table->string('name_si')->nullable();
            $table->string('name_ta')->nullable();
            $table->string('designation_si')->nullable();
            $table->string('designation_ta')->nullable();
        });

        DB::table('employees')->whereNull('name_si')->update(['name_si' => DB::raw('name')]);
        DB::table('employees')->whereNull('name_ta')->update(['name_ta' => DB::raw('name')]);
        DB::table('employees')->whereNull('designation_si')->update(['designation_si' => DB::raw('designation')]);
        DB::table('employees')->whereNull('designation_ta')->update(['designation_ta' => DB::raw('designation')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['name_si', 'name_ta', 'designation_si', 'designation_ta']);
        });
    }
};
