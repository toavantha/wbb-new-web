<?php

namespace Database\Seeders;

use App\Models\Career;
use App\Models\ContactMessage;
use App\Models\NewsItem;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $initialAdmin = config('cms.initial_admin');
        $hasInitialAdminConfiguration = collect($initialAdmin)->contains(fn (?string $value): bool => filled($value));

        if ($hasInitialAdminConfiguration) {
            if (
                ! filled($initialAdmin['name'])
                || ! filled($initialAdmin['email'])
                || ! filled($initialAdmin['password'])
            ) {
                throw new RuntimeException('CMS_ADMIN_NAME, CMS_ADMIN_EMAIL, and CMS_ADMIN_PASSWORD must all be configured to seed an initial administrator.');
            }

            if (mb_strlen($initialAdmin['password']) < 12) {
                throw new RuntimeException('CMS_ADMIN_PASSWORD must be at least 12 characters long.');
            }

            $admin = User::updateOrCreate(
                ['email' => $initialAdmin['email']],
                [
                    'name' => $initialAdmin['name'],
                    'password' => $initialAdmin['password'],
                    'role' => 'admin',
                    'is_approved' => true,
                ]
            );
            $admin->forceFill(['email_verified_at' => now()])->save();
        }

        // 2. News Items & Announcements
        $news = [
            [
                'title' => 'Aswesuma Phase II: List of Eligible Families 2025/2026 Released',
                'slug' => 'aswesuma-phase-ii-eligible-list-released',
                'category' => 'Announcement',
                'excerpt' => 'The Welfare Benefits Board has officially published the secondary list of eligible families for the Aswesuma welfare benefit scheme across all 331 Divisional Secretariats.',
                'content' => 'The Welfare Benefits Board (WBB), functioning under the Ministry of Finance, Planning and Economic Development, has released the comprehensive list of eligible beneficiaries under Phase II of the Aswesuma Welfare Program. Over 1.8 million families island-wide have been selected following rigorous socio-economic scoring criteria based on 22 indicators. The list is accessible via the official IWMS portal (iwms.wbb.gov.lk) and eServices platform (eservices.wbb.gov.lk), as well as through notice boards displayed at respective Divisional Secretariats.',
                'image_url' => 'http://wbb.gov.lk/assets/img/sliderimgone.jpg',
                'published_date' => '2026-03-15',
                'is_featured' => true,
                'is_published' => true,
            ],
            [
                'title' => 'Mandatory Bank Account Verification for Direct Welfare Transfers',
                'slug' => 'mandatory-bank-account-verification',
                'category' => 'Notice',
                'excerpt' => 'All Aswesuma beneficiaries are notified to confirm that their individual bank accounts at Bank of Ceylon, Peoples Bank, or National Savings Bank are actively linked to their NIC.',
                'content' => 'In accordance with the transparent direct cash transfer mandate of the Welfare Benefits Act No. 24 of 2002, all subsidy payments are deposited directly into individual bank accounts held by the head of the eligible family. Beneficiaries are urged to visit their nearest State Bank branch (BOC, Peoples Bank, or NSB) with their original National Identity Card to ensure seamless receipt of their monthly allowance.',
                'image_url' => 'http://wbb.gov.lk/images/homepage/bank_m2.jpg',
                'published_date' => '2026-03-02',
                'is_featured' => true,
                'is_published' => true,
            ],
            [
                'title' => 'Field Census and Enumerator Training Workshops in Northern and North Central Provinces',
                'slug' => 'field-census-training-workshops',
                'category' => 'Workshop',
                'excerpt' => 'Training of Trainers (TOT) workshops successfully concluded across Vavuniya, Mannar, Mullaitivu, and Anuradhapura District Secretariats.',
                'content' => 'A series of comprehensive technical training sessions was conducted by senior officials of the Welfare Benefits Board in collaboration with district and divisional administrators. The workshops focused on the updated digital field survey application, verification protocols, and mobile data integrity checks designed to eradicate inclusion and exclusion errors in welfare disbursements.',
                'image_url' => 'http://wbb.gov.lk/images/homepage/20240418tot1.jpeg',
                'published_date' => '2026-02-18',
                'is_featured' => false,
                'is_published' => true,
            ],
            [
                'title' => 'Press Briefing: Strengthening Social Safety Nets in Sri Lanka',
                'slug' => 'press-briefing-strengthening-social-safety-nets',
                'category' => 'Press Release',
                'excerpt' => 'Chairman of the Welfare Benefits Board highlights key milestone reforms in national welfare distribution and integrated data systems.',
                'content' => 'At a media conference convened at the J.R. Jayawardena Centre in Colombo, WBB Chairman Nimal Kotawalagedara, alongside representatives from the Ministry of Finance and the Treasury, emphasized that the Integrated Welfare Management System (IWMS) is currently operating as the country’s authoritative social registry. The Chairman reaffirmed the Board’s dedication to ensuring that public funds are directed strictly to genuine citizens in need.',
                'image_url' => 'http://wbb.gov.lk/web_images/about/assimg/PressPhase2_1.jpeg',
                'published_date' => '2026-01-28',
                'is_featured' => false,
                'is_published' => true,
            ],
        ];

        foreach ($news as $n) {
            NewsItem::updateOrCreate(['slug' => $n['slug']], $n);
        }

        // 3. Publications, Acts, Circulars & Downloads
        $publications = [
            [
                'title' => 'Welfare Benefits Act, No. 24 of 2002',
                'category' => 'Act',
                'document_number' => 'Act No. 24 of 2002',
                'language' => 'English',
                'description' => 'An Act to provide for the payment of welfare benefit schemes; the establishment of the Welfare Benefits Board; and for matters connected therewith or incidental thereto.',
                'file_url' => 'http://wbb.gov.lk/backend/assets/publications/Welfare_Benefits_Act_No_24_of_2002.pdf',
                'file_size' => '1.8 MB',
                'published_date' => '2002-09-30',
                'is_active' => true,
            ],
            [
                'title' => 'Extraordinary Gazette No. 1952/22 (Operationalization of WBB)',
                'category' => 'Gazette',
                'document_number' => 'Gazette 1952/22',
                'language' => 'Trilingual',
                'description' => 'Gazette notification of the Democratic Socialist Republic of Sri Lanka bringing the Welfare Benefits Act into formal operation.',
                'file_url' => 'http://wbb.gov.lk/backend/assets/publications/Gazette_1952_22.pdf',
                'file_size' => '850 KB',
                'published_date' => '2016-02-02',
                'is_active' => true,
            ],
            [
                'title' => 'Circular WBB/2026/01: Criteria & Allowances for Low Income Beneficiaries',
                'category' => 'Circular',
                'document_number' => 'Circular 01/2026',
                'language' => 'English',
                'description' => 'Guidelines issued to District Secretaries and Divisional Secretaries regarding disbursement thresholds and re-enumeration criteria.',
                'file_url' => 'http://wbb.gov.lk/Circular',
                'file_size' => '1.2 MB',
                'published_date' => '2026-01-05',
                'is_active' => true,
            ],
            [
                'title' => 'Aswesuma Phase II - Appeals and Objections Submission Form (Sinhala)',
                'category' => 'Application Form',
                'document_number' => 'WBB/APP/2026/S',
                'language' => 'Sinhala',
                'description' => 'Official application form for lodging claims and objections regarding the secondary eligibility list of Aswesuma Phase II (අස්වැසුම දෙවන අදියර - අභියාචනා සහ විරෝධතා අයඳුම්පත).',
                'file_url' => 'http://wbb.gov.lk/backend/assets/publications/web/newAppS.pdf',
                'file_size' => '420 KB',
                'published_date' => '2026-01-10',
                'is_active' => true,
            ],
            [
                'title' => 'Aswesuma Phase II - Appeals and Objections Submission Form (Tamil)',
                'category' => 'Application Form',
                'document_number' => 'WBB/APP/2026/T',
                'language' => 'Tamil',
                'description' => 'Official application form for lodging claims and objections regarding the secondary eligibility list of Aswesuma Phase II (அஸ்வெசும இரண்டாம் கட்டம் - மேன்முறையீட்டு விண்ணப்பப்படிவம்).',
                'file_url' => 'http://wbb.gov.lk/backend/assets/publications/web/newAppT.pdf',
                'file_size' => '440 KB',
                'published_date' => '2026-01-10',
                'is_active' => true,
            ],
            [
                'title' => 'Aswesuma Field Enumerator Handbook (Sinhala Guide Line)',
                'category' => 'Handbook',
                'document_number' => 'WBB/HB/2025/01',
                'language' => 'Sinhala',
                'description' => 'Comprehensive manual and operational guidelines for field census enumerators and Divisional Secretariat personnel (අස්වැසුම දෙවන අදියර සඳහා වූ සංගණන අත්පොත).',
                'file_url' => 'http://wbb.gov.lk/backend/assets/publications/wbb%20ciculars/handbook/Sinhala%20Guide%20Line.pdf',
                'file_size' => '3.4 MB',
                'published_date' => '2025-11-20',
                'is_active' => true,
            ],
            [
                'title' => 'Aswesuma Field Enumerator Handbook (Tamil Guide Line)',
                'category' => 'Handbook',
                'document_number' => 'WBB/HB/2025/02',
                'language' => 'Tamil',
                'description' => 'Comprehensive manual and operational guidelines for field census enumerators (அஸ்வெசும இரண்டாம் கட்டத்தின் வழிகாட்டற் கையேடு).',
                'file_url' => 'http://wbb.gov.lk/backend/assets/publications/wbb%20ciculars/handbook/Final%20Tamil%20%20Guide%20Line.pdf',
                'file_size' => '3.6 MB',
                'published_date' => '2025-11-20',
                'is_active' => true,
            ],
        ];

        foreach ($publications as $p) {
            Publication::updateOrCreate(['title' => $p['title']], $p);
        }

        // 4. Careers
        $careers = [
            [
                'title' => 'Senior Database Administrator / Systems Architect',
                'department' => 'Integrated Welfare Management System (IWMS)',
                'employment_type' => 'Full-Time',
                'location' => 'Head Office, Colombo 07',
                'salary_scale' => 'Government Approved Scale (MM 1-1)',
                'description' => 'Responsible for maintaining high availability, security, and scaling for the nationwide Integrated Welfare Management System database containing millions of household records.',
                'qualifications' => 'B.Sc. in Computer Science or Software Engineering with a minimum of 5 years demonstrated experience in enterprise SQL, high-volume transactions, and cloud infrastructure.',
                'closing_date' => '2026-11-30',
                'attachment_url' => 'http://wbb.gov.lk/backend/assets/publications/TOR.pdf',
                'is_active' => true,
            ],
            [
                'title' => 'District Welfare Coordination Officer (Multiple Districts)',
                'department' => 'Field Operations & Monitoring',
                'employment_type' => 'Contract',
                'location' => 'District Secretariats Island-wide',
                'salary_scale' => 'JM 1-1 Government Scale',
                'description' => 'Coordinate with Divisional Secretaries, supervise field enumerators, manage beneficiary inquiries, and facilitate regional appeal committee deliberations.',
                'qualifications' => 'Degree from a recognized university in Social Sciences, Public Administration, or Statistics with at least 2 years field coordination experience.',
                'closing_date' => '2026-12-15',
                'attachment_url' => 'http://wbb.gov.lk/backend/assets/publications/TOR.pdf',
                'is_active' => true,
            ],
            [
                'title' => 'Legal and Compliance Officer',
                'department' => 'Legal & Compliance Division',
                'employment_type' => 'Full-Time',
                'location' => 'Head Office, Colombo 07',
                'salary_scale' => 'MM 1-1 Government Scale',
                'description' => 'Oversee statutory compliance under the Welfare Benefits Act No. 24 of 2002 and handle formal regulatory correspondence with government institutions.',
                'qualifications' => 'Attorney-at-Law or LL.B from a recognized university with at least 3 years of post-qualification legal practice in administrative or public law.',
                'closing_date' => '2026-11-25',
                'attachment_url' => 'http://wbb.gov.lk/backend/assets/publications/TOR.pdf',
                'is_active' => true,
            ],
        ];

        foreach ($careers as $c) {
            Career::updateOrCreate(['title' => $c['title']], $c);
        }

        // 5. Contact Inquiries
        $contacts = [
            [
                'name' => 'Sunil Wickramasinghe',
                'email' => 'sunil.w@gmail.com',
                'phone' => '0773344551',
                'subject' => 'Inquiry regarding Elderly Allowance inclusion in Aswesuma Phase II',
                'message' => 'My mother is 74 years old and was previously receiving elderly support. We submitted our details during the census in Kaduwela. Could you please confirm if elderly allowance will be continued under the same bank account?',
                'status' => 'Responded',
                'admin_notes' => 'Informed citizen via email that elderly allowance is integrated into the monthly direct payment system.',
            ],
            [
                'name' => 'Fathima Farhana',
                'email' => 'farhana.f@gmail.com',
                'phone' => '0718877665',
                'subject' => 'Divisional Secretariat contact detail query',
                'message' => 'Requesting clarification on contact details for the social welfare committee desk at Beruwala DS division.',
                'status' => 'New',
                'admin_notes' => null,
            ],
        ];

        foreach ($contacts as $cnt) {
            ContactMessage::updateOrCreate(['email' => $cnt['email'], 'subject' => $cnt['subject']], $cnt);
        }
    }
}
