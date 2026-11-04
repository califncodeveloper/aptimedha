<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TaxonomySeeder extends Seeder
{
    /**
     * Run the database seeds for client-approved taxonomy.
     */
    public function run(): void
    {
        $taxonomy = [
            [
                'name' => 'Arithmetic Aptitude',
                'slug' => 'arithmetic-aptitude',
                'icon' => 'calculator',
                'description' => 'Comprehensive mathematical ability questions covering number systems, commercial math, and algebraic problems.',
                'display_order' => 1,
                'subjects' => [
                    [
                        'name' => 'Number & Algebra',
                        'slug' => 'number-and-algebra',
                        'description' => 'Fundamental numerical operations, algebraic properties, sequences and approximations.',
                        'display_order' => 1,
                        'topics' => ['Number System', 'Divisibility Rules', 'HCF and LCM', 'Fractions and Decimals', 'Simplification and Approximation', 'Squares, Cubes and Roots', 'Surds and Indices', 'Algebra and Equations', 'Progressions', 'Logarithms'],
                    ],
                    [
                        'name' => 'Commercial Arithmetic',
                        'slug' => 'commercial-arithmetic',
                        'description' => 'Business calculations including percentage, profit-loss, interest, and ratio analysis.',
                        'display_order' => 2,
                        'topics' => ['Percentages', 'Profit and Loss', 'Ratio and Proportion', 'Averages', 'Problems on Ages', 'Partnership', 'Simple and Compound Interest', 'Discounts'],
                    ],
                    [
                        'name' => 'Time, Speed & Work',
                        'slug' => 'time-speed-work',
                        'description' => 'Work-rate formulations, cistern flow problems, and relative speed distance dynamics.',
                        'display_order' => 3,
                        'topics' => ['Time and Work', 'Work and Wages', 'Pipes and Cisterns', 'Time, Speed and Distance', 'Problems on Trains', 'Boats and Streams', 'Races and Games'],
                    ],
                    [
                        'name' => 'Advanced & Modern Math',
                        'slug' => 'advanced-modern-math',
                        'description' => 'Combinatorics, probability distribution, planar geometry and mensuration.',
                        'display_order' => 4,
                        'topics' => ['Permutations and Combinations', 'Probability', 'Geometry', 'Mensuration 2D & 3D', 'Data Sufficiency'],
                    ],
                ],
            ],
            [
                'name' => 'Data Interpretation',
                'slug' => 'data-interpretation',
                'icon' => 'bar-chart-2',
                'description' => 'Analyze tables, bar charts, line graphs, pie charts, and complex caselets for campus placements and competitive exams.',
                'display_order' => 2,
                'subjects' => [
                    [
                        'name' => 'Charts & Visual Data',
                        'slug' => 'charts-visual-data',
                        'description' => 'Visual representations of data across tabular, bar, linear and angular formats.',
                        'display_order' => 1,
                        'topics' => ['Table Charts', 'Bar Graphs', 'Line Graphs', 'Pie Charts', 'Radar and Web Charts', 'Mixed Charts'],
                    ],
                    [
                        'name' => 'Caselets & Logic DI',
                        'slug' => 'caselets-logic-di',
                        'description' => 'Paragraph-based numerical situations and set-theoretic data sufficiency.',
                        'display_order' => 2,
                        'topics' => ['Caselet DI', 'Missing Data Interpretation', 'Data Sufficiency', 'Venn Diagram DI'],
                    ],
                ],
            ],
            [
                'name' => 'Logical Reasoning',
                'slug' => 'logical-reasoning',
                'icon' => 'puzzle',
                'description' => 'Sharpen deductive logic, pattern recognition, series, puzzles, and analytical reasoning.',
                'display_order' => 3,
                'subjects' => [
                    [
                        'name' => 'Analytical Reasoning',
                        'slug' => 'analytical-reasoning',
                        'description' => 'Systematic arrangement problems, relationship paths, and symbolic sequences.',
                        'display_order' => 1,
                        'topics' => ['Number Series', 'Letter and Symbol Series', 'Analogies', 'Coding and Decoding', 'Blood Relations', 'Direction Sense Test', 'Seating Arrangements', 'Puzzles'],
                    ],
                    [
                        'name' => 'Logical & Critical Deduction',
                        'slug' => 'critical-deduction',
                        'description' => 'Formal syllogisms, argument validity, premise evaluations and course of actions.',
                        'display_order' => 2,
                        'topics' => ['Syllogisms', 'Statement and Assumptions', 'Statement and Conclusions', 'Cause and Effect', 'Course of Action', 'Assertion and Reason'],
                    ],
                    [
                        'name' => 'Non-Verbal Reasoning',
                        'slug' => 'non-verbal-reasoning',
                        'description' => 'Abstract spatial manipulations, mirror geometries and pattern completions.',
                        'display_order' => 3,
                        'topics' => ['Pattern Completion', 'Figure Matrix', 'Paper Folding and Cutting', 'Cubes and Dice', 'Mirror and Water Images'],
                    ],
                ],
            ],
            [
                'name' => 'Verbal Ability',
                'slug' => 'verbal-ability',
                'icon' => 'message-square',
                'description' => 'Reading comprehension, vocabulary mastery, sentence correction, and verbal logic.',
                'display_order' => 4,
                'subjects' => [
                    [
                        'name' => 'Vocabulary & Usage',
                        'slug' => 'vocabulary-usage',
                        'description' => 'Lexical breadth, figurative idioms, precision words and orthography.',
                        'display_order' => 1,
                        'topics' => ['Synonyms and Antonyms', 'Idioms and Phrases', 'One Word Substitutions', 'Contextual Vocabulary', 'Spellings'],
                    ],
                    [
                        'name' => 'Grammar & Structure',
                        'slug' => 'grammar-structure',
                        'description' => 'Syntactic accuracy, clause modification, error detection and sentence arrangement.',
                        'display_order' => 2,
                        'topics' => ['Spotting Errors', 'Sentence Correction', 'Sentence Improvement', 'Fill in the Blanks', 'Para Jumbles'],
                    ],
                    [
                        'name' => 'Reading & Comprehension',
                        'slug' => 'reading-comprehension',
                        'description' => 'Passage analysis, main thesis detection, inference derivation and tone assessment.',
                        'display_order' => 3,
                        'topics' => ['Passage Reading', 'Critical Reading', 'Summary and Theme Detection', 'Cloze Test'],
                    ],
                ],
            ],
            [
                'name' => 'English',
                'slug' => 'english',
                'icon' => 'file-text',
                'description' => 'Comprehensive English grammar rules, parts of speech, voice, speech conversion, and sentence structure.',
                'display_order' => 5,
                'subjects' => [
                    [
                        'name' => 'Core Grammar Rules',
                        'slug' => 'core-grammar',
                        'description' => 'Foundational linguistic mechanics, verbal concordance and syntactic transformation.',
                        'display_order' => 1,
                        'topics' => ['Parts of Speech', 'Tenses and Verb Forms', 'Subject-Verb Agreement', 'Active and Passive Voice', 'Direct and Indirect Speech', 'Articles and Prepositions'],
                    ],
                ],
            ],
            [
                'name' => 'General Knowledge',
                'slug' => 'general-knowledge',
                'icon' => 'globe',
                'description' => 'Static GK, Indian polity, geography, science, history, awards, and monthly current affairs updates.',
                'display_order' => 6,
                'subjects' => [
                    [
                        'name' => 'Static GK',
                        'slug' => 'static-gk',
                        'description' => 'Historical timelines, administrative constitution, geography and scientific milestones.',
                        'display_order' => 1,
                        'topics' => ['Indian History', 'Indian Polity & Constitution', 'World and Indian Geography', 'General Science', 'Indian Economy', 'Inventions and Discoveries', 'Books and Authors', 'Awards and Honours', 'Sports and Games'],
                    ],
                    [
                        'name' => 'Current Affairs',
                        'slug' => 'current-affairs',
                        'description' => 'National developments, global partnerships and policy implementations.',
                        'display_order' => 2,
                        'topics' => ['Monthly Current Affairs (2026)', 'National News', 'International Affairs', 'Science & Technology Updates', 'Government Schemes'],
                    ],
                ],
            ],
            [
                'name' => 'Programming',
                'slug' => 'programming',
                'icon' => 'code',
                'description' => 'Language fundamentals, syntax, object-oriented concepts, data structures, and algorithms.',
                'display_order' => 7,
                'subjects' => [
                    [
                        'name' => 'Programming Languages',
                        'slug' => 'languages',
                        'description' => 'Core idioms, pointer arithmetic, object models and language memory architectures.',
                        'display_order' => 1,
                        'topics' => ['C Programming', 'C++', 'Java Programming', 'Python', 'JavaScript'],
                    ],
                    [
                        'name' => 'Core Computer Science',
                        'slug' => 'core-cs',
                        'description' => 'Computational complexity, query execution, process scheduling and network protocol stacks.',
                        'display_order' => 2,
                        'topics' => ['Data Structures', 'Algorithms', 'Database Management Systems (DBMS / SQL)', 'Operating Systems', 'Computer Networks', 'Object-Oriented Programming (OOP)'],
                    ],
                ],
            ],
            [
                'name' => 'Engineering',
                'slug' => 'engineering',
                'icon' => 'cpu',
                'description' => 'Branch-wise technical MCQs for engineering campus recruitments and gate exams.',
                'display_order' => 8,
                'subjects' => [
                    [
                        'name' => 'Engineering Branches',
                        'slug' => 'engineering-branches',
                        'description' => 'Discipline-specific engineering problem sets across electrical, mechanical and civil curricula.',
                        'display_order' => 1,
                        'topics' => ['Computer Science & IT', 'Electronics & Communication', 'Electrical Engineering', 'Mechanical Engineering', 'Civil Engineering'],
                    ],
                ],
            ],
        ];

        foreach ($taxonomy as $catData) {
            $category = Category::firstOrCreate(
                ['slug' => $catData['slug']],
                [
                    'name' => $catData['name'],
                    'icon' => $catData['icon'],
                    'description' => $catData['description'],
                    'display_order' => $catData['display_order'],
                    'is_active' => true,
                ]
            );

            foreach ($catData['subjects'] as $subjData) {
                $subject = Subject::firstOrCreate(
                    ['slug' => $subjData['slug']],
                    [
                        'category_id' => $category->id,
                        'name' => $subjData['name'],
                        'description' => $subjData['description'],
                        'display_order' => $subjData['display_order'],
                        'is_active' => true,
                    ]
                );

                $topicOrder = 1;
                foreach ($subjData['topics'] as $topicName) {
                    $topicSlug = Str::slug($topicName);
                    Topic::firstOrCreate(
                        ['slug' => $topicSlug],
                        [
                            'category_id' => $category->id,
                            'subject_id' => $subject->id,
                            'name' => $topicName,
                            'description' => "Practice comprehensive MCQs on {$topicName}.",
                            'display_order' => $topicOrder++,
                            'total_questions_count' => 0,
                            'is_active' => true,
                        ]
                    );
                }
            }
        }
    }
}

