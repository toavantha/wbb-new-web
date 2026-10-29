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
        Schema::create('government_affiliations', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->string('name_si');
            $table->string('name_ta');
            $table->string('title_en');
            $table->string('title_si');
            $table->string('title_ta');
            $table->string('ministry_en');
            $table->string('ministry_si');
            $table->string('ministry_ta');
            $table->string('image_path')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $timestamp = now();
        DB::table('government_affiliations')->insert([
            [
                'name_en' => 'Anura Kumara Dissanayake',
                'name_si' => 'අනුර කුමාර දිසානායක',
                'name_ta' => 'அநுர குமார திசாநாயக்க',
                'title_en' => 'President of Sri Lanka • Minister of Finance, Planning & Economic Development',
                'title_si' => 'ශ්‍රී ලංකා ජනාධිපති • මුදල්, සැලසුම් සහ ආර්ථික සංවර්ධන අමාත්‍ය',
                'title_ta' => 'இலங்கை ஜனாதிபதி • நிதி, திட்டமிடல் மற்றும் பொருளாதார அபிவிருத்தி அமைச்சர்',
                'ministry_en' => 'Ministry of Finance, Planning and Economic Development',
                'ministry_si' => 'මුදල්, සැලසුම් සහ ආර්ථික සංවර්ධන අමාත්‍යාංශය',
                'ministry_ta' => 'நிதி, திட்டமிடல் மற்றும் பொருளாதார அபிவிருத்தி அமைச்சு',
                'image_path' => 'images/anura-kumara-dissanayake.jpg',
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name_en' => 'Prof. Anil Jayantha Fernando',
                'name_si' => 'Prof. Anil Jayantha Fernando',
                'name_ta' => 'Prof. Anil Jayantha Fernando',
                'title_en' => 'Deputy Minister of Economic Development',
                'title_si' => 'ආර්ථික සංවර්ධන නියෝජ්‍ය අමාත්‍ය',
                'title_ta' => 'பொருளாதார அபிவிருத்தி பிரதி அமைச்சர்',
                'ministry_en' => 'Ministry of Finance, Planning and Economic Development',
                'ministry_si' => 'මුදල්, සැලසුම් සහ ආර්ථික සංවර්ධන අමාත්‍යාංශය',
                'ministry_ta' => 'நிதி, திட்டமிடல் மற்றும் பொருளாதார அபிவிருத்தி அமைச்சு',
                'image_path' => 'http://wbb.gov.lk/backend/assets/publications/web/DepMin2.jpg',
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name_en' => 'Dr. Harshana Suriyapperuma',
                'name_si' => 'Dr. Harshana Suriyapperuma',
                'name_ta' => 'Dr. Harshana Suriyapperuma',
                'title_en' => 'Secretary to the Treasury',
                'title_si' => 'භාණ්ඩාගාර ලේකම්',
                'title_ta' => 'திறைசேரி செயலாளர்',
                'ministry_en' => 'Ministry of Finance, Planning and Economic Development',
                'ministry_si' => 'මුදල්, සැලසුම් සහ ආර්ථික සංවර්ධන අමාත්‍යාංශය',
                'ministry_ta' => 'நிதி, திட்டமிடல் மற்றும் பொருளாதார அபிவிருத்தி அமைச்சு',
                'image_path' => 'http://wbb.gov.lk/backend/assets/publications/web/DepMin1.jpg',
                'sort_order' => 3,
                'is_active' => true,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('government_affiliations');
    }
};
