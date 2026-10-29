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
        Schema::create('board_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('position');
            $table->string('email')->nullable();
            $table->string('image_path')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $timestamp = now();
        DB::table('board_members')->insert([
            [
                'name' => 'Nimal Kotawalagedara',
                'position' => 'chairman',
                'email' => 'commissioner.wbb@mo.treasury.gov.lk',
                'image_path' => 'http://wbb.gov.lk/web_images/about/assimg/Chairman.png',
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'Rizna Anees',
                'position' => 'member',
                'email' => 'riznaanees284@gmail.com',
                'image_path' => 'http://wbb.gov.lk/web_images/about/assimg/Member_Rizna.png',
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'T. M. S. P. K. Thennakoon',
                'position' => 'member',
                'email' => 'info@iwms.wbb.gov.lk',
                'image_path' => 'http://wbb.gov.lk/web_images/about/assimg/Member_Thennakoon.png',
                'sort_order' => 3,
                'is_active' => true,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'Ayanthi De Silva',
                'position' => 'member',
                'email' => 'info@iwms.wbb.gov.lk',
                'image_path' => 'http://wbb.gov.lk/web_images/about/assimg/Member_Ayanthi.png',
                'sort_order' => 4,
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
        Schema::dropIfExists('board_members');
    }
};
