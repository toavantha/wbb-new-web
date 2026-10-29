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
        Schema::create('welfare_schemes', function (Blueprint $table) {
            $table->id();
            $table->json('content');
            $table->unsignedInteger('monthly_allowance');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $schemes = [
            [
                'monthly_allowance' => 15000,
                'sort_order' => 1,
                'content' => [
                    'en' => [
                        'name' => 'Aswesuma - Severely Poor',
                        'badge' => 'Highest Priority',
                        'duration' => '3 Years with review',
                        'description' => 'Targeted at households facing acute socio-economic deprivation, lack of basic amenities, food insecurity, and zero stable livelihood sources.',
                        'indicators' => ['Zero stable employment', 'Sub-standard housing conditions', 'Malnutrition indicators', 'Severely disabled dependents'],
                    ],
                    'si' => [
                        'name' => 'අස්වැසුම - අතිශය දිළිඳු',
                        'badge' => 'ඉහළම ප්‍රමුඛතාව',
                        'duration' => 'සමාලෝචනය සමඟ වසර 3ක්',
                        'description' => 'දැඩි සමාජ-ආර්ථික අහිමිවීම්, මූලික පහසුකම් නොමැතිකම, ආහාර අනාරක්ෂිතභාවය සහ ස්ථාවර ජීවනෝපාය මාර්ග නොමැති පවුල් සඳහා ඉලක්කගත කර ඇත.',
                        'indicators' => ['ස්ථාවර රැකියාවක් නොමැති වීම', 'ප්‍රමිතියෙන් පහළ නිවාස තත්ත්වය', 'පෝෂණ ඌනතා දර්ශක', 'දැඩි ආබාධ සහිත යැපෙන්නන්'],
                    ],
                    'ta' => [
                        'name' => 'அஸ்வெசும - மிகவும் வறியோர்',
                        'badge' => 'மிக உயர்ந்த முன்னுரிமை',
                        'duration' => 'மீளாய்வுடன் 3 ஆண்டுகள்',
                        'description' => 'கடுமையான சமூக-பொருளாதாரப் பற்றாக்குறை, அடிப்படை வசதிகள் இல்லாமை, உணவுப் பாதுகாப்பின்மை மற்றும் நிலையான வாழ்வாதாரமின்மையை எதிர்கொள்ளும் குடும்பங்களுக்காக இலக்கிடப்பட்டுள்ளது.',
                        'indicators' => ['நிலையான தொழில் இல்லை', 'தரமற்ற வீட்டு நிலைமைகள்', 'ஊட்டச்சத்துக் குறைபாட்டு குறிகாட்டிகள்', 'கடுமையான இயலாமையுள்ள தங்கியிருப்போர்'],
                    ],
                ],
            ],
            [
                'monthly_allowance' => 8500,
                'sort_order' => 2,
                'content' => [
                    'en' => [
                        'name' => 'Aswesuma - Poor',
                        'badge' => 'Core Beneficiary',
                        'duration' => '3 Years',
                        'description' => 'Provided to low-income families whose aggregated multi-dimensional poverty index places them below the national vulnerability threshold.',
                        'indicators' => ['Daily wage earner income', 'Inadequate asset base', 'High dependent ratio', 'Basic education burden'],
                    ],
                    'si' => [
                        'name' => 'අස්වැසුම - දිළිඳු',
                        'badge' => 'ප්‍රධාන ප්‍රතිලාභී',
                        'duration' => 'වසර 3ක්',
                        'description' => 'සමස්ත බහුමාන දරිද්‍රතා දර්ශකය ජාතික අවදානම් සීමාවට පහළින් පවතින අඩු ආදායම්ලාභී පවුල් සඳහා ලබාදේ.',
                        'indicators' => ['දෛනික වැටුප් ආදායම', 'ප්‍රමාණවත් නොවන වත්කම්', 'ඉහළ යැපුම් අනුපාතය', 'මූලික අධ්‍යාපන වියදම් බර'],
                    ],
                    'ta' => [
                        'name' => 'அஸ்வெசும - வறியோர்',
                        'badge' => 'முக்கிய பயனாளர்',
                        'duration' => '3 ஆண்டுகள்',
                        'description' => 'ஒட்டுமொத்த பல்பரிமாண வறுமைக் குறியீடு தேசிய பாதிப்பு வரம்பிற்குக் கீழே உள்ள குறைந்த வருமானக் குடும்பங்களுக்கு வழங்கப்படுகிறது.',
                        'indicators' => ['நாள் சம்பள வருமானம்', 'போதிய சொத்து அடிப்படை இல்லை', 'உயர் தங்கியிருப்பு விகிதம்', 'அடிப்படைக் கல்விச் செலவுச் சுமை'],
                    ],
                ],
            ],
            [
                'monthly_allowance' => 5000,
                'sort_order' => 3,
                'content' => [
                    'en' => [
                        'name' => 'Elderly Allowance (Senior Citizens 70+)',
                        'badge' => 'Categorical Benefit',
                        'duration' => 'Continuous',
                        'description' => 'Dedicated welfare support for elderly senior citizens aged 70 years and above who are without permanent pension benefits.',
                        'indicators' => ['70+ years of age verified by NIC', 'Low household income bracket'],
                    ],
                    'si' => [
                        'name' => 'වැඩිහිටි දීමනාව (වයස 70ට වැඩි)',
                        'badge' => 'විශේෂ කාණ්ඩ ප්‍රතිලාභය',
                        'duration' => 'අඛණ්ඩව',
                        'description' => 'ස්ථිර විශ්‍රාම වැටුප් ප්‍රතිලාභ නොමැති වයස අවුරුදු 70 සහ ඊට වැඩි ජ්‍යෙෂ්ඨ පුරවැසියන් සඳහා විශේෂ සුබසාධන සහාය.',
                        'indicators' => ['ජාතික හැඳුනුම්පතෙන් තහවුරු කළ වයස 70+', 'අඩු ගෘහස්ථ ආදායම් කාණ්ඩය'],
                    ],
                    'ta' => [
                        'name' => 'முதியோர் கொடுப்பனவு (70 வயதுக்கு மேல்)',
                        'badge' => 'வகைசார் நன்மை',
                        'duration' => 'தொடர்ச்சியாக',
                        'description' => 'நிரந்தர ஓய்வூதிய நன்மைகள் இல்லாத 70 வயது மற்றும் அதற்கு மேற்பட்ட மூத்த குடிமக்களுக்கான பிரத்யேக நலன்புரி ஆதரவு.',
                        'indicators' => ['தேசிய அடையாள அட்டையால் உறுதிப்படுத்தப்பட்ட 70+ வயது', 'குறைந்த குடும்ப வருமானப் பிரிவு'],
                    ],
                ],
            ],
            [
                'monthly_allowance' => 7500,
                'sort_order' => 4,
                'content' => [
                    'en' => [
                        'name' => 'Persons with Disabilities Allowance',
                        'badge' => 'Special Care',
                        'duration' => 'Continuous',
                        'description' => 'Specialized financial assistance for citizens diagnosed with severe physical, intellectual, or sensory impairments.',
                        'indicators' => ['Medical board certification', 'Enrolled in district social service registry'],
                    ],
                    'si' => [
                        'name' => 'ආබාධ සහිත පුද්ගලයින් සඳහා දීමනාව',
                        'badge' => 'විශේෂ සත්කාර',
                        'duration' => 'අඛණ්ඩව',
                        'description' => 'දැඩි ශාරීරික, බුද්ධිමය හෝ සංවේදන දුර්වලතා ඇති බවට හඳුනාගත් පුරවැසියන් සඳහා විශේෂිත මූල්‍ය සහාය.',
                        'indicators' => ['වෛද්‍ය මණ්ඩල සහතිකය', 'දිස්ත්‍රික් සමාජ සේවා ලේඛනයේ ලියාපදිංචිය'],
                    ],
                    'ta' => [
                        'name' => 'மாற்றுத்திறனாளிகளுக்கான கொடுப்பனவு',
                        'badge' => 'விசேட பராமரிப்பு',
                        'duration' => 'தொடர்ச்சியாக',
                        'description' => 'கடுமையான உடல், அறிவுசார் அல்லது புலனுணர்வு குறைபாடுகள் உள்ளதாகக் கண்டறியப்பட்ட குடிமக்களுக்கான சிறப்பு நிதி உதவி.',
                        'indicators' => ['மருத்துவக் குழு சான்றிதழ்', 'மாவட்ட சமூக சேவைப் பதிவேட்டில் பதிவு'],
                    ],
                ],
            ],
            [
                'monthly_allowance' => 7500,
                'sort_order' => 5,
                'content' => [
                    'en' => [
                        'name' => 'CKDu (Kidney Disease) Patient Allowance',
                        'badge' => 'Healthcare Relief',
                        'duration' => 'Continuous with clinical review',
                        'description' => 'Special healthcare support allowance for families affected by Chronic Kidney Disease of Unknown Etiology (CKDu), particularly in high-risk agricultural zones.',
                        'indicators' => ['Government hospital clinical diagnosis', 'Continuous dialysis / medical treatment record'],
                    ],
                    'si' => [
                        'name' => 'CKDu (වකුගඩු රෝග) රෝගී දීමනාව',
                        'badge' => 'සෞඛ්‍ය සහන',
                        'duration' => 'සායනික සමාලෝචනය සමඟ අඛණ්ඩව',
                        'description' => 'විශේෂයෙන් ඉහළ අවදානම් කෘෂිකාර්මික ප්‍රදේශවල, හේතුව නොදන්නා නිදන්ගත වකුගඩු රෝගයෙන් (CKDu) පීඩාවට පත් පවුල් සඳහා විශේෂ සෞඛ්‍ය සහාය දීමනාව.',
                        'indicators' => ['රජයේ රෝහලේ සායනික රෝග විනිශ්චය', 'අඛණ්ඩ ඩයලிசිස් / ප්‍රතිකාර වාර්තාව'],
                    ],
                    'ta' => [
                        'name' => 'CKDu (சிறுநீரக நோய்) நோயாளர் கொடுப்பனவு',
                        'badge' => 'சுகாதார நிவாரணம்',
                        'duration' => 'மருத்துவ மீளாய்வுடன் தொடர்ச்சியாக',
                        'description' => 'குறிப்பாக அதிக ஆபத்துள்ள விவசாய வலயங்களில், காரணம் தெரியாத நாள்பட்ட சிறுநீரக நோயால் (CKDu) பாதிக்கப்பட்ட குடும்பங்களுக்கான விசேட சுகாதார ஆதரவுக் கொடுப்பனவு.',
                        'indicators' => ['அரச மருத்துவமனை மருத்துவ நோயறிதல்', 'தொடர்ச்சியான டயாலிசிஸ் / சிகிச்சைப் பதிவு'],
                    ],
                ],
            ],
        ];

        $timestamp = now();
        foreach ($schemes as $scheme) {
            DB::table('welfare_schemes')->insert([
                'content' => json_encode($scheme['content'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
                'monthly_allowance' => $scheme['monthly_allowance'],
                'sort_order' => $scheme['sort_order'],
                'is_active' => true,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('welfare_schemes');
    }
};
