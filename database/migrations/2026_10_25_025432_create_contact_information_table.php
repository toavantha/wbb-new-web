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
        Schema::create('contact_information', function (Blueprint $table) {
            $table->id();
            $table->text('address');
            $table->string('hotline', 50);
            $table->string('hotline_hours_en');
            $table->string('hotline_hours_si');
            $table->string('hotline_hours_ta');
            $table->string('office_phone', 100);
            $table->string('email_primary');
            $table->string('email_secondary');
            $table->text('map_embed_url');
            $table->timestamps();
        });

        $timestamp = now();
        DB::table('contact_information')->insert([
            'id' => 1,
            'address' => "J.R. Jayawardena Centre,\n191 Srimath Anagarika Dharmapala Mawatha,\nTown Hall, Colombo 00700, Sri Lanka",
            'hotline' => '1924',
            'hotline_hours_en' => 'Operates weekdays 8:30 AM – 4:30 PM',
            'hotline_hours_si' => 'සතියේ දිනවල පෙ.ව. 8:30 – ප.ව. 4:30 දක්වා ක්‍රියාත්මක වේ',
            'hotline_hours_ta' => 'வார நாட்களில் காலை 8:30 – மாலை 4:30 வரை செயற்படும்',
            'office_phone' => '+94 11 267 2252 / +94 11 267 2253',
            'email_primary' => 'info@iwms.wbb.gov.lk',
            'email_secondary' => 'commissioner.wbb@mo.treasury.gov.lk',
            'map_embed_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.7871181170262!2d79.8586938740485!3d6.916036418479284!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae259694e7568af%3A0x8b11cf3a574dfb82!2sJ%20R%20Jayawardena%20Centre%2C%20Town%20Hall!5e0!3m2!1sen!2slk!4v1712052631491!5m2!1sen!2slk',
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_information');
    }
};
