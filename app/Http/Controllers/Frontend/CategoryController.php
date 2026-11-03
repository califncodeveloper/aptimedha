<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\View\View;

/**
 * Handles public category browsing and topic taxonomy.
 */
class CategoryController
{
    /**
     * Get the sample category and topic presentation catalogue.
     */
    protected function getSampleCategories(): array
    {
        return [
            'arithmetic-aptitude' => (object)[
                'name' => 'Arithmetic Aptitude',
                'slug' => 'arithmetic-aptitude',
                'icon' => 'calculator',
                'badge' => 'Core Aptitude',
                'mcqs_count' => 1250,
                'description' => 'Comprehensive mathematical ability questions covering number systems, commercial math, and algebraic problems.',
                'subjects' => [
                    (object)[
                        'name' => 'Number & Algebra',
                        'slug' => 'number-and-algebra',
                        'topics' => ['Number System', 'Divisibility Rules', 'HCF and LCM', 'Fractions and Decimals', 'Simplification and Approximation', 'Squares, Cubes and Roots', 'Surds and Indices', 'Algebra and Equations', 'Progressions', 'Logarithms']
                    ],
                    (object)[
                        'name' => 'Commercial Arithmetic',
                        'slug' => 'commercial-arithmetic',
                        'topics' => ['Percentages', 'Profit and Loss', 'Ratio and Proportion', 'Averages', 'Problems on Ages', 'Partnership', 'Simple and Compound Interest', 'Discounts']
                    ],
                    (object)[
                        'name' => 'Time, Speed & Work',
                        'slug' => 'time-speed-work',
                        'topics' => ['Time and Work', 'Work and Wages', 'Pipes and Cisterns', 'Time, Speed and Distance', 'Problems on Trains', 'Boats and Streams', 'Races and Games']
                    ],
                    (object)[
                        'name' => 'Advanced & Modern Math',
                        'slug' => 'advanced-modern-math',
                        'topics' => ['Permutations and Combinations', 'Probability', 'Geometry', 'Mensuration 2D & 3D', 'Data Sufficiency']
                    ],
                ]
            ],
            'data-interpretation' => (object)[
                'name' => 'Data Interpretation',
                'slug' => 'data-interpretation',
                'icon' => 'bar-chart-2',
                'badge' => 'Data & Analytics',
                'mcqs_count' => 840,
                'description' => 'Analyze tables, bar charts, line graphs, pie charts, and complex caselets for campus placements and competitive exams.',
                'subjects' => [
                    (object)[
                        'name' => 'Charts & Visual Data',
                        'slug' => 'charts-visual-data',
                        'topics' => ['Table Charts', 'Bar Graphs', 'Line Graphs', 'Pie Charts', 'Radar and Web Charts', 'Mixed Charts']
                    ],
                    (object)[
                        'name' => 'Caselets & Logic DI',
                        'slug' => 'caselets-logic-di',
                        'topics' => ['Caselet DI', 'Missing Data Interpretation', 'Data Sufficiency', 'Venn Diagram DI']
                    ]
                ]
            ],
            'logical-reasoning' => (object)[
                'name' => 'Logical Reasoning',
                'slug' => 'logical-reasoning',
                'icon' => 'puzzle',
                'badge' => 'Reasoning',
                'mcqs_count' => 1120,
                'description' => 'Sharpen deductive logic, pattern recognition, series, puzzles, and analytical reasoning.',
                'subjects' => [
                    (object)[
                        'name' => 'Analytical Reasoning',
                        'slug' => 'analytical-reasoning',
                        'topics' => ['Number Series', 'Letter and Symbol Series', 'Analogies', 'Coding and Decoding', 'Blood Relations', 'Direction Sense Test', 'Seating Arrangements', 'Puzzles']
                    ],
                    (object)[
                        'name' => 'Logical & Critical Deduction',
                        'slug' => 'critical-deduction',
                        'topics' => ['Syllogisms', 'Statement and Assumptions', 'Statement and Conclusions', 'Cause and Effect', 'Course of Action', 'Assertion and Reason']
                    ],
                    (object)[
                        'name' => 'Non-Verbal Reasoning',
                        'slug' => 'non-verbal-reasoning',
                        'topics' => ['Pattern Completion', 'Figure Matrix', 'Paper Folding and Cutting', 'Cubes and Dice', 'Mirror and Water Images']
                    ]
                ]
            ],
            'verbal-ability' => (object)[
                'name' => 'Verbal Ability',
                'slug' => 'verbal-ability',
                'icon' => 'message-square',
                'badge' => 'Language & Communication',
                'mcqs_count' => 950,
                'description' => 'Reading comprehension, vocabulary mastery, sentence correction, and verbal logic.',
                'subjects' => [
                    (object)[
                        'name' => 'Vocabulary & Usage',
                        'slug' => 'vocabulary-usage',
                        'topics' => ['Synonyms and Antonyms', 'Idioms and Phrases', 'One Word Substitutions', 'Contextual Vocabulary', 'Spellings']
                    ],
                    (object)[
                        'name' => 'Grammar & Structure',
                        'slug' => 'grammar-structure',
                        'topics' => ['Spotting Errors', 'Sentence Correction', 'Sentence Improvement', 'Fill in the Blanks', 'Para Jumbles']
                    ],
                    (object)[
                        'name' => 'Reading & Comprehension',
                        'slug' => 'reading-comprehension',
                        'topics' => ['Passage Reading', 'Critical Reading', 'Summary and Theme Detection', 'Cloze Test']
                    ]
                ]
            ],
            'english' => (object)[
                'name' => 'English',
                'slug' => 'english',
                'icon' => 'file-text',
                'badge' => 'Grammar & Usage',
                'mcqs_count' => 680,
                'description' => 'Comprehensive English grammar rules, parts of speech, voice, speech conversion, and sentence structure.',
                'subjects' => [
                    (object)[
                        'name' => 'Core Grammar Rules',
                        'slug' => 'core-grammar',
                        'topics' => ['Parts of Speech', 'Tenses and Verb Forms', 'Subject-Verb Agreement', 'Active and Passive Voice', 'Direct and Indirect Speech', 'Articles and Prepositions']
                    ]
                ]
            ],
            'general-knowledge' => (object)[
                'name' => 'General Knowledge',
                'slug' => 'general-knowledge',
                'icon' => 'globe',
                'badge' => 'GK & Awareness',
                'mcqs_count' => 1400,
                'description' => 'Static GK, Indian polity, geography, science, history, awards, and monthly current affairs updates.',
                'subjects' => [
                    (object)[
                        'name' => 'Static GK',
                        'slug' => 'static-gk',
                        'topics' => ['Indian History', 'Indian Polity & Constitution', 'World and Indian Geography', 'General Science', 'Indian Economy', 'Inventions and Discoveries', 'Books and Authors', 'Awards and Honours', 'Sports and Games']
                    ],
                    (object)[
                        'name' => 'Current Affairs',
                        'slug' => 'current-affairs',
                        'topics' => ['Monthly Current Affairs (2026)', 'National News', 'International Affairs', 'Science & Technology Updates', 'Government Schemes']
                    ]
                ]
            ],
            'programming' => (object)[
                'name' => 'Programming',
                'slug' => 'programming',
                'icon' => 'code',
                'badge' => 'Technical & Coding',
                'mcqs_count' => 1600,
                'description' => 'Language fundamentals, syntax, object-oriented concepts, data structures, and algorithms.',
                'subjects' => [
                    (object)[
                        'name' => 'Programming Languages',
                        'slug' => 'languages',
                        'topics' => ['C Programming', 'C++', 'Java Programming', 'Python', 'JavaScript']
                    ],
                    (object)[
                        'name' => 'Core Computer Science',
                        'slug' => 'core-cs',
                        'topics' => ['Data Structures', 'Algorithms', 'Database Management Systems (DBMS / SQL)', 'Operating Systems', 'Computer Networks', 'Object-Oriented Programming (OOP)']
                    ]
                ]
            ],
            'engineering' => (object)[
                'name' => 'Engineering',
                'slug' => 'engineering',
                'icon' => 'cpu',
                'badge' => 'Technical Branches',
                'mcqs_count' => 1100,
                'description' => 'Branch-wise technical MCQs for engineering campus recruitments and gate exams.',
                'subjects' => [
                    (object)[
                        'name' => 'Engineering Branches',
                        'slug' => 'engineering-branches',
                        'topics' => ['Computer Science & IT', 'Electronics & Communication', 'Electrical Engineering', 'Mechanical Engineering', 'Civil Engineering']
                    ]
                ]
            ]
        ];
    }

    /**
     * Display a listing of categories.
     */
    public function index(): View
    {
        $categories = $this->getSampleCategories();

        return view('frontend.categories.index', compact('categories'));
    }

    /**
     * Display the specified category with subjects and topics.
     */
    public function show(string $category): View
    {
        $categories = $this->getSampleCategories();
        $cat = $categories[$category] ?? null;

        if (!$cat) {
            // Find by close slug or use first as fallback
            $cat = (object)[
                'name' => ucwords(str_replace('-', ' ', $category)),
                'slug' => $category,
                'icon' => 'folder',
                'badge' => 'Subject',
                'mcqs_count' => 250,
                'description' => 'Practice topic-wise multiple-choice questions for ' . ucwords(str_replace('-', ' ', $category)) . '.',
                'subjects' => [
                    (object)[
                        'name' => 'Fundamentals & Core Concepts',
                        'slug' => 'fundamentals',
                        'topics' => ['Overview & Basics', 'Standard Formulas & Rules', 'Solved Practice Examples', 'Mock Test Paper']
                    ]
                ]
            ];
        }

        return view('frontend.categories.show', ['category' => $cat]);
    }
}
