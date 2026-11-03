<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Handles public MCQ browsing and interactive practice interface.
 */
class McqController
{
    /**
     * Get sample questions for interactive client presentation.
     */
    protected function getSampleQuestions(): array
    {
        return [
            (object)[
                'id' => 1,
                'number' => 1,
                'category_name' => 'Arithmetic Aptitude',
                'category_slug' => 'arithmetic-aptitude',
                'subject_name' => 'Commercial Arithmetic',
                'topic_name' => 'Percentages',
                'topic_slug' => 'percentages',
                'question' => 'What is 25% of 240?',
                'has_image' => false,
                'image_url' => null,
                'options' => [
                    'A' => '40',
                    'B' => '50',
                    'C' => '60',
                    'D' => '80',
                ],
                'correct_option' => 'C',
                'explanation' => '25% = 25/100 = 1/4. Therefore, 25% of 240 = (1/4) × 240 = 60.',
                'bookmarks_count' => 142,
                'discussions_count' => 12,
            ],
            (object)[
                'id' => 2,
                'number' => 2,
                'category_name' => 'Arithmetic Aptitude',
                'category_slug' => 'arithmetic-aptitude',
                'subject_name' => 'Commercial Arithmetic',
                'topic_name' => 'Percentages',
                'topic_slug' => 'percentages',
                'question' => 'A value increases from 80 to 100. What is the percentage increase?',
                'has_image' => false,
                'image_url' => null,
                'options' => [
                    'A' => '10%',
                    'B' => '20%',
                    'C' => '25%',
                    'D' => '50%',
                ],
                'correct_option' => 'C',
                'explanation' => 'Initial value = 80, Final value = 100. Increase = 100 - 80 = 20. Percentage Increase = (Increase / Initial Value) × 100 = (20 / 80) × 100 = 25%.',
                'bookmarks_count' => 89,
                'discussions_count' => 7,
            ],
            (object)[
                'id' => 3,
                'number' => 3,
                'category_name' => 'Arithmetic Aptitude',
                'category_slug' => 'arithmetic-aptitude',
                'subject_name' => 'Commercial Arithmetic',
                'topic_name' => 'Percentages',
                'topic_slug' => 'percentages',
                'question' => 'If 30% of a number is 120, what is 45% of that number?',
                'has_image' => false,
                'image_url' => null,
                'options' => [
                    'A' => '160',
                    'B' => '180',
                    'C' => '190',
                    'D' => '200',
                ],
                'correct_option' => 'B',
                'explanation' => 'Let the number be x. 0.30 × x = 120 ⇒ x = 120 / 0.30 = 400. Now, 45% of 400 = 0.45 × 400 = 180.',
                'bookmarks_count' => 64,
                'discussions_count' => 5,
            ],
            (object)[
                'id' => 4,
                'number' => 4,
                'category_name' => 'Arithmetic Aptitude',
                'category_slug' => 'arithmetic-aptitude',
                'subject_name' => 'Commercial Arithmetic',
                'topic_name' => 'Percentages',
                'topic_slug' => 'percentages',
                'question' => 'The price of sugar increases by 20%. By what percentage must a household reduce its consumption so as not to increase the expenditure on sugar?',
                'has_image' => false,
                'image_url' => null,
                'options' => [
                    'A' => '16.67%',
                    'B' => '20%',
                    'C' => '25%',
                    'D' => '15%',
                ],
                'correct_option' => 'A',
                'explanation' => 'Reduction percentage = [R / (100 + R)] × 100 = [20 / (100 + 20)] × 100 = (20 / 120) × 100 = 100 / 6 = 16.67%.',
                'bookmarks_count' => 110,
                'discussions_count' => 9,
            ],
            (object)[
                'id' => 5,
                'number' => 5,
                'category_name' => 'Arithmetic Aptitude',
                'category_slug' => 'arithmetic-aptitude',
                'subject_name' => 'Commercial Arithmetic',
                'topic_name' => 'Percentages',
                'topic_slug' => 'percentages',
                'question' => 'In an examination, a candidate must secure 40% marks to pass. A student gets 220 marks and fails by 20 marks. What are the maximum marks for the examination?',
                'has_image' => false,
                'image_url' => null,
                'options' => [
                    'A' => '500',
                    'B' => '600',
                    'C' => '700',
                    'D' => '800',
                ],
                'correct_option' => 'B',
                'explanation' => 'Passing marks = 220 + 20 = 240. Since 40% of Maximum Marks = 240, Maximum Marks = (240 / 40) × 100 = 600.',
                'bookmarks_count' => 77,
                'discussions_count' => 3,
            ],
            (object)[
                'id' => 6,
                'number' => 6,
                'category_name' => 'Arithmetic Aptitude',
                'category_slug' => 'arithmetic-aptitude',
                'subject_name' => 'Commercial Arithmetic',
                'topic_name' => 'Percentages',
                'topic_slug' => 'percentages',
                'question' => 'Two numbers are respectively 20% and 50% more than a third number. The ratio of the two numbers is:',
                'has_image' => false,
                'image_url' => null,
                'options' => [
                    'A' => '2 : 5',
                    'B' => '3 : 5',
                    'C' => '4 : 5',
                    'D' => '6 : 7',
                ],
                'correct_option' => 'C',
                'explanation' => 'Let the third number be 100. First number = 120, Second number = 150. Ratio = 120 : 150 = 12 : 15 = 4 : 5.',
                'bookmarks_count' => 95,
                'discussions_count' => 4,
            ],
            (object)[
                'id' => 7,
                'number' => 7,
                'category_name' => 'Arithmetic Aptitude',
                'category_slug' => 'arithmetic-aptitude',
                'subject_name' => 'Commercial Arithmetic',
                'topic_name' => 'Percentages',
                'topic_slug' => 'percentages',
                'question' => 'A man spends 35% of his income on food, 25% on children education and 80% of the remaining on house rent. What percent of his full income is left with him?',
                'has_image' => false,
                'image_url' => null,
                'options' => [
                    'A' => '8%',
                    'B' => '10%',
                    'C' => '12%',
                    'D' => '14%',
                ],
                'correct_option' => 'A',
                'explanation' => 'Total initial expenditure = 35% + 25% = 60%. Remaining = 40%. House rent = 80% of 40% = 32%. Final left with him = 40% - 32% = 8%.',
                'bookmarks_count' => 84,
                'discussions_count' => 6,
            ],
            (object)[
                'id' => 8,
                'number' => 8,
                'category_name' => 'Arithmetic Aptitude',
                'category_slug' => 'arithmetic-aptitude',
                'subject_name' => 'Commercial Arithmetic',
                'topic_name' => 'Percentages',
                'topic_slug' => 'percentages',
                'question' => 'If the radius of a circle is increased by 50%, its area is increased by:',
                'has_image' => false,
                'image_url' => null,
                'options' => [
                    'A' => '100%',
                    'B' => '125%',
                    'C' => '150%',
                    'D' => '75%',
                ],
                'correct_option' => 'B',
                'explanation' => 'Using successive percentage formula: Net % change = x + y + (xy / 100) = 50 + 50 + (50 × 50 / 100) = 100 + 25 = 125%.',
                'bookmarks_count' => 135,
                'discussions_count' => 11,
            ],
            (object)[
                'id' => 9,
                'number' => 9,
                'category_name' => 'Arithmetic Aptitude',
                'category_slug' => 'arithmetic-aptitude',
                'subject_name' => 'Commercial Arithmetic',
                'topic_name' => 'Percentages',
                'topic_slug' => 'percentages',
                'question' => 'When 60 is subtracted from 60% of a number, the result is 60. The number is:',
                'has_image' => false,
                'image_url' => null,
                'options' => [
                    'A' => '120',
                    'B' => '150',
                    'C' => '180',
                    'D' => '200',
                ],
                'correct_option' => 'D',
                'explanation' => 'Let the number be x. 0.60x - 60 = 60 ⇒ 0.60x = 120 ⇒ x = 120 / 0.60 = 200.',
                'bookmarks_count' => 52,
                'discussions_count' => 2,
            ],
            (object)[
                'id' => 10,
                'number' => 10,
                'category_name' => 'Arithmetic Aptitude',
                'category_slug' => 'arithmetic-aptitude',
                'subject_name' => 'Commercial Arithmetic',
                'topic_name' => 'Percentages',
                'topic_slug' => 'percentages',
                'question' => 'Fresh fruit contains 68% water and dry fruit contains 20% water. How much dry fruit can be obtained from 100 kg of fresh fruits?',
                'has_image' => false,
                'image_url' => null,
                'options' => [
                    'A' => '32 kg',
                    'B' => '40 kg',
                    'C' => '52 kg',
                    'D' => '80 kg',
                ],
                'correct_option' => 'B',
                'explanation' => 'In 100 kg fresh fruit, pulp = (100 - 68)% of 100 kg = 32 kg. In dry fruit, pulp is (100 - 20)% = 80%. Let dry fruit weight be D. 80% of D = 32 kg ⇒ D = (32 × 100) / 80 = 40 kg.',
                'bookmarks_count' => 160,
                'discussions_count' => 14,
            ],
        ];
    }

    /**
     * Display a listing of MCQs (practice view).
     */
    public function index(Request $request): View
    {
        $allQuestions = $this->getSampleQuestions();
        $selectedCategory = $request->query('category', 'arithmetic-aptitude');
        $selectedTopic = $request->query('topic', 'percentages');

        // Sidebar topics matching mockup
        $sidebarTopics = [
            (object)['name' => 'Percentages', 'slug' => 'percentages', 'count' => 150, 'active' => true],
            (object)['name' => 'Averages', 'slug' => 'averages', 'count' => 120, 'active' => false],
            (object)['name' => 'Profit and Loss', 'slug' => 'profit-and-loss', 'count' => 140, 'active' => false],
            (object)['name' => 'Ratio and Proportion', 'slug' => 'ratio-and-proportion', 'count' => 110, 'active' => false],
            (object)['name' => 'Time and Work', 'slug' => 'time-and-work', 'count' => 130, 'active' => false],
            (object)['name' => 'Pipes and Cisterns', 'slug' => 'pipes-and-cisterns', 'count' => 85, 'active' => false],
            (object)['name' => 'Time, Speed & Distance', 'slug' => 'time-speed-distance', 'count' => 145, 'active' => false],
            (object)['name' => 'Problems on Trains', 'slug' => 'problems-on-trains', 'count' => 90, 'active' => false],
            (object)['name' => 'Probability', 'slug' => 'probability', 'count' => 105, 'active' => false],
            (object)['name' => 'Permutations & Combinations', 'slug' => 'permutations-combinations', 'count' => 95, 'active' => false],
        ];

        return view('frontend.mcq.index', [
            'mcqs' => $allQuestions,
            'categoryName' => 'Arithmetic Aptitude',
            'categorySlug' => 'arithmetic-aptitude',
            'topicName' => 'Percentages',
            'topicSlug' => 'percentages',
            'sidebarTopics' => $sidebarTopics,
            'currentSection' => 1,
            'totalSections' => 2,
            'currentPage' => 1,
            'totalPages' => 10,
            'totalQuestions' => 150,
        ]);
    }

    /**
     * Display a single MCQ.
     */
    public function show(string $mcq): View
    {
        $allQuestions = $this->getSampleQuestions();
        $id = (int)$mcq;
        $question = $allQuestions[0];
        foreach ($allQuestions as $q) {
            if ($q->id === $id || $q->number === $id) {
                $question = $q;
                break;
            }
        }

        return view('frontend.mcq.show', [
            'mcq' => $question,
            'previousMcq' => $question->id > 1 ? (object)['id' => $question->id - 1] : null,
            'nextMcq' => $question->id < 10 ? (object)['id' => $question->id + 1] : null,
        ]);
    }
}
