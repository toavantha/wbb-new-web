<?php

namespace Database\Seeders;

use App\Models\Procurement;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ProcurementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $today = Carbon::today();

        $items = [
            [
                'title' => 'Procurement of Server Infrastructure & Cloud Hosting Services for IWMS',
                'title_si' => 'IWMS පද්ධතිය සඳහා සර්වර් යටිතල පහසුකම් සහ ක්ලවුඩ් සත්කාරක සේවා ප්‍රසම්පාදනය',
                'title_ta' => 'IWMS க்கான சேவையக உள்கட்டமைப்பு மற்றும் கிளவுட் ஹோஸ்டிங் சேவைகளைக் கொள்முதல் செய்தல்',
                'reference_no' => 'WBB/PROC/2026/01',
                'category' => 'Procurement Notice',
                'description' => 'National Competitive Bidding (NCB) for the supply, configuration, and maintenance of high-availability cloud servers for Aswesuma Social Safety Net Scheme.',
                'description_si' => 'අස්වැසුම ජාතික සමාජ ආරක්ෂණ යෝජනා ක්‍රමය සඳහා ඉහළ කාර්යක්ෂමතාවයකින් යුත් සර්වර් සැපයීම සහ නඩත්තුව.',
                'description_ta' => 'அஸ்வெசும தேசிய சமூகப் பாதுகாப்புத் திட்டத்திற்கான சேவையக வழங்கல் மற்றும் பராமரிப்பு.',
                'published_date' => $today->copy()->subDays(10),
                'deadline_date' => $today->copy()->addDays(20),
                'original_deadline_date' => null,
                'is_active' => true,
            ],
            [
                'title' => 'Tender Document: Supply and Delivery of 500 Desktop Computers and UPS Units',
                'title_si' => 'ටෙන්ඩර් ලේඛනය: පරිගණක සහ විදුලි උපකරණ 500ක් සැපයීම',
                'title_ta' => 'டெண்டர் ஆவணம்: 500 கணினிகள் மற்றும் மின் உபகரணங்களை வழங்குதல்',
                'reference_no' => 'WBB/TEND/2026/04',
                'category' => 'Tender Document',
                'description' => 'Comprehensive tender bidding guidelines, technical specifications, and submission format for divisional office IT hardware.',
                'description_si' => 'ප්‍රාදේශීය ලේකම් කාර්යාල තොරතුරු තාක්ෂණ උපකරණ සඳහා වන තාක්ෂණික පිරිවිතර සහ ලංසු මාර්ගෝපදේශ.',
                'description_ta' => 'பிரதேச செயலக தகவல் தொழில்நுட்ப உபகரணங்களுக்கான வழிகாட்டல்கள் மற்றும் ஆவணங்கள்.',
                'published_date' => $today->copy()->subDays(15),
                'deadline_date' => $today->copy()->addDays(12),
                'original_deadline_date' => $today->copy()->subDays(1),
                'is_active' => true,
            ],
            [
                'title' => 'Terms of Reference (TOR): Consulting Services for Aswesuma Impact Assessment Survey',
                'title_si' => 'යොමු කොන්දේසි (TOR): අස්වැසුම ප්‍රතිලාභ ඇගයීමේ සමීක්ෂණය සඳහා උපදේශන සේවා',
                'title_ta' => 'அதிகார வரம்புகள் (TOR): அஸ்வெசும தாக்க மதிப்பீட்டு கணக்கெடுப்புக்கான ஆலோசனை சேவைகள்',
                'reference_no' => 'WBB/TOR/2026/08',
                'category' => 'TOR',
                'description' => 'Terms of Reference for independent academic and research institutions to carry out household welfare impact assessment across 331 DS divisions.',
                'description_si' => 'ප්‍රාදේශීය ලේකම් කොට්ඨාස 331 ආවරණය වන පරිදි පවුල් සුබසාධන ඇගයීම සිදු කිරීම සඳහා ස්වාධීන පර්යේෂණ ආයතන වෙතින් යොමු කොන්දේසි.',
                'description_ta' => '331 பிரதேச செயலகப் பிரிவுகளில் குடும்ப நல மதிப்பீட்டை மேற்கொள்வதற்கான சுயாதீன ஆராய்ச்சி நிறுவனங்களுக்கான அதிகார வரம்புகள்.',
                'published_date' => $today->copy()->subDays(8),
                'deadline_date' => $today->copy()->addDays(25),
                'original_deadline_date' => null,
                'is_active' => true,
            ],
            [
                'title' => 'Expression of Interest (EOI): Printing and Distribution of Citizen Guidebooks & Awareness Materials',
                'title_si' => 'පුරවැසි අත්පොත් සහ දැනුවත් කිරීමේ ද්‍රව්‍ය මුද්‍රණය සහ බෙදාහැරීම',
                'title_ta' => 'குடிமக்கள் வழிகாட்டிகள் மற்றும் விழிப்புணர்வு பொருட்களை அச்சிடுதல் மற்றும் விநியோகித்தல்',
                'reference_no' => 'WBB/EOI/2026/12',
                'category' => 'Other',
                'description' => 'Request for expression of interest for printing multi-language citizen guidance booklets for Aswesuma Phase 2 beneficiaries.',
                'description_si' => 'අස්වැසුම දෙවන අදියරේ ප්‍රතිලාභීන් සඳහා ත්‍රෛභාෂා පුරවැසි මාර්ගෝපදේශ මුද්‍රණය සඳහා වන ලංසු කැඳවීම.',
                'description_ta' => 'அஸ்வெசும இரண்டாம் கட்ட பயனாளிகளுக்கான மும்மொழி வழிகாட்டல்களை அச்சிடுவதற்கான அறிவிப்பு.',
                'published_date' => $today->copy()->subDays(5),
                'deadline_date' => $today->copy()->addDays(18),
                'original_deadline_date' => null,
                'is_active' => true,
            ],
            [
                'title' => 'Procurement of Security and Network Firewall Upgrade for WBB Data Center',
                'title_si' => null,
                'title_ta' => null,
                'reference_no' => 'WBB/PROC/2026/02',
                'category' => 'Procurement Notice',
                'description' => 'Sealed bids are invited for enterprise network security hardware and 3-year support license for the Welfare Benefits Board central server farm.',
                'description_si' => null,
                'description_ta' => null,
                'published_date' => $today->copy()->subDays(40),
                'deadline_date' => $today->copy()->subDays(5),
                'original_deadline_date' => null,
                'is_active' => true,
            ],
            [
                'title' => 'Tender for Supply of Office Furniture for Head Office Relocation',
                'title_si' => null,
                'title_ta' => null,
                'reference_no' => 'WBB/TEND/2026/01',
                'category' => 'Tender Document',
                'description' => 'Supply and installation of modular workstations, executive desks, and ergonomic chairs.',
                'description_si' => null,
                'description_ta' => null,
                'published_date' => $today->copy()->subDays(60),
                'deadline_date' => $today->copy()->subDays(15),
                'original_deadline_date' => null,
                'is_active' => true,
            ],
        ];

        foreach ($items as $data) {
            Procurement::create($data);
        }
    }
}
