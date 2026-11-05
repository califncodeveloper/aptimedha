<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BlogSeeder extends Seeder
{
    /**
     * Seed blog categories and real educational articles.
     */
    public function run(): void
    {
        // Ensure admin user exists for author attribution
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $admin = User::updateOrCreate(
                ['email' => 'admin@aptimedha.com'],
                [
                    'name' => 'AptiMedha Editorial Team',
                    'username' => 'editorial',
                    'password' => Hash::make('Admin@12345'),
                    'role' => 'admin',
                ]
            );
        }

        $categories = [
            'Placement Strategy' => [
                'slug' => 'placement-strategy',
                'description' => 'Strategic frameworks and preparation roadmaps for campus and off-campus placements.',
            ],
            'Aptitude Insights' => [
                'slug' => 'aptitude-insights',
                'description' => 'Shortcut techniques, calculation speed methods, and data interpretation insights.',
            ],
            'Interview Prep' => [
                'slug' => 'interview-prep',
                'description' => 'HR interview question answers, behavioral scenarios, and interview etiquette.',
            ],
            'Exam Patterns' => [
                'slug' => 'exam-patterns',
                'description' => 'Detailed breakdowns of national qualifier tests, eligibility patterns, and sectional timings.',
            ],
        ];

        $categoryMap = [];
        foreach ($categories as $name => $data) {
            $categoryMap[$name] = BlogCategory::firstOrCreate(
                ['slug' => $data['slug']],
                ['name' => $name, 'description' => $data['description']]
            );
        }

        $articles = [
            [
                'title' => 'How to Prepare for Aptitude Tests in Placements',
                'slug' => 'how-to-prepare-for-aptitude-tests-in-placements',
                'category' => 'Placement Strategy',
                'author_name' => 'AptiMedha Editorial Team',
                'published_at' => now()->subDays(10),
                'status' => 'published',
                'views_count' => 4230,
                'featured_image_alt' => 'Placement Aptitude Preparation Guide',
                'excerpt' => 'A structured guide to mastering arithmetic, logical deduction, and verbal sections within a 60-day roadmap for campus placements.',
                'meta_title' => 'How to Prepare for Aptitude Tests in Campus Placements | AptiMedha',
                'meta_description' => 'A structured guide to mastering arithmetic, logical deduction, and verbal sections within a 60-day roadmap for campus recruitment drives.',
                'body' => '<p>Campus recruitment aptitude tests are designed to assess candidate speed, numerical accuracy, and deductive problem-solving ability under strict time limits. Most campus recruiters—including major IT service companies, product companies, and core engineering firms—use aptitude assessments as their primary screening filter.</p>
                
<h3>1. The Three Core Pillars of Placement Aptitude</h3>
<p>Preparation should balance three major areas:</p>
<ul>
    <li><strong>Quantitative Aptitude:</strong> Focus heavily on commercial arithmetic: percentages, profit and loss, ratios, averages, and time-and-work. These topics account for more than 50% of typical quantitative sections.</li>
    <li><strong>Logical Reasoning:</strong> Master analytical series, seating arrangements, coding-decoding, blood relations, and syllogisms. Practicing with diagrams and tables speeds up arrangement solving.</li>
    <li><strong>Verbal Ability:</strong> Build a daily reading habit to improve reading comprehension speed and accuracy in error spotting, sentence completion, and para-jumbles.</li>
</ul>

<h3>2. Speed Math and Shortcut Techniques</h3>
<p>Memorize squares up to 30, cubes up to 20, multiplication tables up to 20, and fractional percentage equivalents (e.g., 1/6 = 16.67%, 1/8 = 12.5%). Eliminating manual calculation of basic arithmetic preserves valuable minutes for complex data interpretation problems.</p>

<h3>3. Assessment Strategy: Skip and Circle</h3>
<p>Never get bogged down by a single time-consuming puzzle during an assessment. Allocate 45-60 seconds per question on your initial pass, answering immediate calculations first, and flag multi-step questions for your second pass.</p>',
            ],
            [
                'title' => 'Data Interpretation Tips and Tricks for Speed & Accuracy',
                'slug' => 'data-interpretation-tips-and-tricks',
                'category' => 'Aptitude Insights',
                'author_name' => 'Pooja Nair, Aptitude Specialist',
                'published_at' => now()->subDays(6),
                'status' => 'published',
                'views_count' => 2850,
                'featured_image_alt' => 'Data Interpretation Shortcuts & Tricks',
                'excerpt' => 'Practical calculation shortcuts and percentage approximation techniques to solve complex multi-chart DI sets quickly.',
                'meta_title' => 'Data Interpretation Tips & Calculation Tricks | AptiMedha',
                'meta_description' => 'Practical calculation shortcuts and percentage approximation techniques to solve complex multi-chart DI sets quickly and accurately.',
                'body' => '<p>Data Interpretation tests your ability to extract meaning from tabular data, pie charts, bar diagrams, and caselet narratives. The most common pitfall is computing exact values when questions only require approximate comparisons.</p>
                
<h3>1. Master Percentage Increase / Decrease Formulas</h3>
<p>Always compute percentage changes against the base period: <code>[(Final - Initial) / Initial] × 100</code>. Practice visual estimation for bar graphs and pie charts before punching numbers into scrap paper.</p>

<h3>2. Approximating Complex Ratios</h3>
<p>When comparing fractions such as 432/875 and 621/1190, use reciprocal or fractional rounding (e.g. 430/880 ≈ 49% vs 620/1200 ≈ 51.6%) to immediately determine the larger quantity without dividing by hand.</p>

<h3>3. Tabular Scanning</h3>
<p>Read the question stem first before looking at chart legends. Identify the exact row and column intersections required to avoid cognitive overload.</p>',
            ],
            [
                'title' => 'Common HR Interview Questions and Recommended Responses',
                'slug' => 'common-interview-questions-and-answers',
                'category' => 'Interview Prep',
                'author_name' => 'Siddharth Rao, Career Mentor',
                'published_at' => now()->subDays(3),
                'status' => 'published',
                'views_count' => 3640,
                'featured_image_alt' => 'Common HR Interview Questions and Frameworks',
                'excerpt' => 'Authentic response frameworks for behavioral questions including strengths, weaknesses, teamwork conflicts, and career goals.',
                'meta_title' => 'Common HR Interview Questions & Authentic Answers | AptiMedha',
                'meta_description' => 'Authentic STAR response frameworks for behavioral questions including strengths, weaknesses, teamwork conflicts, and long-term goals.',
                'body' => '<p>The HR interview determines your interpersonal maturity, communication clarity, teamwork orientation, and cultural fit within an organization. Rehearsing scripted answers often sounds robotic; instead, prepare specific anecdotes and structured frameworks.</p>
                
<h3>1. The STAR Technique for Behavioral Scenarios</h3>
<p>Whenever an interviewer asks for an example of overcoming a challenge or resolving a team conflict, frame your reply as:</p>
<ul>
    <li><strong>Situation:</strong> Briefly establish the context or project background.</li>
    <li><strong>Task:</strong> Explain what problem needed solving.</li>
    <li><strong>Action:</strong> Detail the specific actions and initiative you took.</li>
    <li><strong>Result:</strong> State the tangible outcome, metrics, or lessons learned.</li>
</ul>

<h3>2. Handling the "Tell Me About Yourself" Question</h3>
<p>Structure your answer chronologically and relevantly: start with your present academic discipline and technical projects, touch upon key achievements or internships, and conclude with why you are excited about this specific opportunity.</p>',
            ],
            [
                'title' => 'TCS NQT 2026: Complete Syllabus, Sectional Timings & Pattern',
                'slug' => 'tcs-nqt-2026-complete-syllabus-and-pattern',
                'category' => 'Exam Patterns',
                'author_name' => 'AptiMedha Editorial Team',
                'published_at' => now()->subDays(1),
                'status' => 'published',
                'views_count' => 5120,
                'featured_image_alt' => 'TCS NQT 2026 Exam Pattern and Sectional Timing',
                'excerpt' => 'A complete breakdown of cognitive skills, advanced quantitative, and programming assessment sections for 2026 batch candidates.',
                'meta_title' => 'TCS NQT 2026 Complete Syllabus, Pattern & Timings | AptiMedha',
                'meta_description' => 'A complete breakdown of cognitive skills, advanced quantitative, and programming assessment sections for campus candidates.',
                'body' => '<p>TCS National Qualifier Test (NQT) remains one of the largest campus recruitment assessments in the country. Understanding sectional time boundaries and negative marking policies is essential for maximizing your percentile score.</p>

<h3>1. Foundation Section Overview</h3>
<p>The cognitive Foundation section tests numerical ability (20 questions in 25 minutes), verbal ability (25 questions in 25 minutes), and reasoning ability (20 questions in 25 minutes). Accuracy is vital as questions are non-adaptive but strictly timed per sub-section.</p>

<h3>2. Advanced Quantitative & Reasoning</h3>
<p>Candidates aiming for Prime and Digital cadres must perform strongly in advanced topics: permutations & combinations, probability, advanced data interpretation, and algorithmic reasoning.</p>',
            ],
        ];

        foreach ($articles as $art) {
            $cat = $categoryMap[$art['category']];
            Blog::updateOrCreate(
                ['slug' => $art['slug']],
                [
                    'blog_category_id' => $cat->id,
                    'author_id' => $admin->id,
                    'author_name' => $art['author_name'],
                    'title' => $art['title'],
                    'body' => $art['body'],
                    'excerpt' => $art['excerpt'],
                    'featured_image_alt' => $art['featured_image_alt'],
                    'status' => $art['status'],
                    'published_at' => $art['published_at'],
                    'views_count' => $art['views_count'],
                    'meta_title' => $art['meta_title'],
                    'meta_description' => $art['meta_description'],
                ]
            );
        }
    }
}

