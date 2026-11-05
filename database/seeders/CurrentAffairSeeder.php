<?php

namespace Database\Seeders;

use App\Models\CurrentAffair;
use Illuminate\Database\Seeder;

class CurrentAffairSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Safety guard: Do not seed mock/demo current affairs records in production.
        // Genuine issues must be published by administrators via the CMS with verified PDF assets.
        if (app()->environment('production') || config('app.env') === 'production') {
            return;
        }

        $issues = [
            [
                'year' => 2026,
                'month' => 11,
                'title' => 'Monthly Current Affairs – November 2026',
                'description' => '<p>Comprehensive coverage of key national and international events for November 2026. Includes in-depth analysis of government schemes, monetary policy updates, global summit highlights, science & technology milestones, major sports achievements, and high-frequency MCQs tailored for competitive exams and campus placement assessments.</p>',
                'pdf_path' => 'current-affairs/pdfs/aptimedha-current-affairs-november-2026.pdf',
                'pdf_name' => 'AptiMedha-Current-Affairs-November-2026.pdf',
                'preview_image' => null,
                'preview_image_alt' => 'Current Affairs November 2026 Monthly Capsule',
                'file_size_bytes' => 3145728, // 3.0 MB
                'downloads_count' => 1240,
                'is_published' => true,
                'published_at' => now(),
            ],
            [
                'year' => 2026,
                'month' => 10,
                'title' => 'Monthly Current Affairs – October 2026',
                'description' => '<p>Complete current affairs capsule for October 2026. Features Nobel Prize 2026 announcements, macroeconomic indicators, defense exercises, environmental summits, national appointments, and practice questions for campus recruitment tests.</p>',
                'pdf_path' => 'current-affairs/pdfs/aptimedha-current-affairs-october-2026.pdf',
                'pdf_name' => 'AptiMedha-Current-Affairs-October-2026.pdf',
                'preview_image' => null,
                'preview_image_alt' => 'Current Affairs October 2026 Monthly Capsule',
                'file_size_bytes' => 2831155, // 2.7 MB
                'downloads_count' => 2410,
                'is_published' => true,
                'published_at' => now()->subMonth(),
            ],
            [
                'year' => 2026,
                'month' => 9,
                'title' => 'Monthly Current Affairs – September 2026',
                'description' => '<p>September 2026 monthly digest featuring global economic summits, space exploration updates, technological breakthroughs in AI and semiconductor manufacturing, cultural milestones, and curated aptitude practice sets.</p>',
                'pdf_path' => 'current-affairs/pdfs/aptimedha-current-affairs-september-2026.pdf',
                'pdf_name' => 'AptiMedha-Current-Affairs-September-2026.pdf',
                'preview_image' => null,
                'preview_image_alt' => 'Current Affairs September 2026 Monthly Capsule',
                'file_size_bytes' => 3407872, // 3.25 MB
                'downloads_count' => 3180,
                'is_published' => true,
                'published_at' => now()->subMonths(2),
            ],
            [
                'year' => 2026,
                'month' => 8,
                'title' => 'Monthly Current Affairs – August 2026',
                'description' => '<p>August 2026 current affairs review covering national independence celebrations, key bilateral agreements, banking & financial sector reforms, sports championships, and state government initiatives.</p>',
                'pdf_path' => 'current-affairs/pdfs/aptimedha-current-affairs-august-2026.pdf',
                'pdf_name' => 'AptiMedha-Current-Affairs-August-2026.pdf',
                'preview_image' => null,
                'preview_image_alt' => 'Current Affairs August 2026 Monthly Capsule',
                'file_size_bytes' => 2936012, // 2.8 MB
                'downloads_count' => 2950,
                'is_published' => true,
                'published_at' => now()->subMonths(3),
            ],
            [
                'year' => 2025,
                'month' => 12,
                'title' => 'Monthly Current Affairs – Year End Review 2025',
                'description' => '<p>Year-end retrospective of 2025: key national policy shifts, constitutional amendments, global summits, climate agreements, and comprehensive round-up of the most significant general knowledge questions of the year.</p>',
                'pdf_path' => 'current-affairs/pdfs/aptimedha-current-affairs-december-2025.pdf',
                'pdf_name' => 'AptiMedha-Current-Affairs-December-2025.pdf',
                'preview_image' => null,
                'preview_image_alt' => 'Current Affairs December 2025 Year End Review',
                'file_size_bytes' => 4718592, // 4.5 MB
                'downloads_count' => 5420,
                'is_published' => true,
                'published_at' => now()->subMonths(11),
            ],
        ];

        foreach ($issues as $issue) {
            CurrentAffair::updateOrCreate(
                [
                    'year' => $issue['year'],
                    'month' => $issue['month'],
                ],
                $issue
            );
        }
    }
}

