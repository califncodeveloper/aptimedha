<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\View\View;

/**
 * Handles public blog browsing and display.
 */
class BlogController
{
    /**
     * Get sample blog articles.
     */
    protected function getSamplePosts(): array
    {
        return [
            'how-to-prepare-for-aptitude-tests-in-placements' => (object)[
                'title' => 'How to Prepare for Aptitude Tests in Placements',
                'slug' => 'how-to-prepare-for-aptitude-tests-in-placements',
                'category' => 'Placement Strategy',
                'author' => 'AptiMedha Editorial Team',
                'date' => 'March 12, 2026',
                'read_time' => '6 min read',
                'excerpt' => 'A structured guide to mastering arithmetic, logical deduction, and verbal sections within a 60-day roadmap for campus placements.',
                'body' => '
                    <p>Campus recruitment aptitude tests are designed to assess candidate speed, numerical accuracy, and deductive problem-solving ability under strict time limits. Most campus recruiters—including major IT service companies, product companies, and core engineering firms—use aptitude assessments as their primary screening filter.</p>
                    
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
                    <p>Never get bogged down by a single time-consuming puzzle during an assessment. Allocate 45-60 seconds per question on your initial pass, answering immediate calculations first, and flag multi-step questions for your second pass.</p>
                '
            ],
            'data-interpretation-tips-and-tricks' => (object)[
                'title' => 'Data Interpretation Tips and Tricks for Speed & Accuracy',
                'slug' => 'data-interpretation-tips-and-tricks',
                'category' => 'Aptitude Insights',
                'author' => 'Pooja Nair, Aptitude Specialist',
                'date' => 'March 8, 2026',
                'read_time' => '5 min read',
                'excerpt' => 'Practical calculation shortcuts and percentage approximation techniques to solve complex multi-chart DI sets quickly.',
                'body' => '
                    <p>Data Interpretation tests your ability to extract meaning from tabular data, pie charts, bar diagrams, and caselet narratives. The most common pitfall is computing exact values when questions only require approximate comparisons.</p>
                    
                    <h3>1. Master Percentage Increase / Decrease Formulas</h3>
                    <p>Always compute percentage changes against the base period: <code>[(Final - Initial) / Initial] × 100</code>. Practice visual estimation for bar graphs and pie charts before punching numbers into scrap paper.</p>

                    <h3>2. Approximating Complex Ratios</h3>
                    <p>When comparing fractions such as 432/875 and 621/1190, use reciprocal or fractional rounding (e.g. 430/880 ≈ 49% vs 620/1200 ≈ 51.6%) to immediately determine the larger quantity.</p>
                '
            ],
            'common-interview-questions-and-answers' => (object)[
                'title' => 'Common HR Interview Questions and Recommended Responses',
                'slug' => 'common-interview-questions-and-answers',
                'category' => 'Interview Prep',
                'author' => 'Siddharth Rao, Career Mentor',
                'date' => 'March 5, 2026',
                'read_time' => '7 min read',
                'excerpt' => 'Authentic response frameworks for behavioral questions including strengths, weaknesses, teamwork conflicts, and career goals.',
                'body' => '
                    <p>The HR interview determines your interpersonal maturity, communication clarity, teamwork orientation, and cultural fit within an organization. Rehearsing scripted answers often sounds robotic; instead, prepare specific anecdotes and structured frameworks.</p>
                    
                    <h3>1. The STAR Technique for Behavioral Scenarios</h3>
                    <p>Whenever an interviewer asks for an example of overcoming a challenge or resolving a team conflict, frame your reply as:</p>
                    <ul>
                        <li><strong>Situation:</strong> Briefly establish the context or project background.</li>
                        <li><strong>Task:</strong> Explain what problem needed solving.</li>
                        <li><strong>Action:</strong> Detail the specific actions and initiative you took.</li>
                        <li><strong>Result:</strong> State the tangible outcome, metrics, or lessons learned.</li>
                    </ul>
                '
            ],
            'tcs-nqt-2026-complete-syllabus-and-pattern' => (object)[
                'title' => 'TCS NQT 2026: Complete Syllabus, Sectional Timings & Pattern',
                'slug' => 'tcs-nqt-2026-complete-syllabus-and-pattern',
                'category' => 'Exam Patterns',
                'author' => 'AptiMedha Editorial Team',
                'date' => 'February 26, 2026',
                'read_time' => '8 min read',
                'excerpt' => 'A complete breakdown of cognitive skills, advanced quantitative, and programming assessment sections for 2026 batch candidates.',
                'body' => '
                    <p>TCS National Qualifier Test (NQT) remains one of the largest campus recruitment assessments in the country. Understanding sectional time boundaries and negative marking policies is essential for maximizing your percentile score.</p>
                '
            ]
        ];
    }

    /**
     * Display a listing of blog posts.
     */
    public function index(): View
    {
        $posts = $this->getSamplePosts();

        return view('frontend.blog.index', compact('posts'));
    }

    /**
     * Display the specified blog post.
     */
    public function show(string $post): View
    {
        $posts = $this->getSamplePosts();
        $article = $posts[$post] ?? null;

        if (!$article) {
            $article = (object)[
                'title' => ucwords(str_replace('-', ' ', $post)),
                'slug' => $post,
                'category' => 'Placement Guidance',
                'author' => 'AptiMedha Team',
                'date' => date('F d, Y'),
                'read_time' => '5 min read',
                'excerpt' => 'Essential aptitude and interview preparation guidance for students and job seekers.',
                'body' => '<p>Detailed educational guide on ' . ucwords(str_replace('-', ' ', $post)) . '. Practice regularly on AptiMedha to master your skills.</p>'
            ];
        }

        // Related posts
        $related = array_slice(array_filter($posts, fn($p) => $p->slug !== $article->slug), 0, 3);

        return view('frontend.blog.show', [
            'post' => $article,
            'related' => $related,
        ]);
    }
}
