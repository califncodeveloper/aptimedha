<?php

namespace Database\Seeders;

use App\Models\PlacementGuide;
use Illuminate\Database\Seeder;

class PlacementGuideSeeder extends Seeder
{
    /**
     * Run the database seeds for client-approved placement preparation guides.
     */
    public function run(): void
    {
        $guides = [
            [
                'title' => 'Comprehensive 4-Stage Placement Preparation Roadmap',
                'slug' => 'placement-preparation-roadmap',
                'category' => 'roadmap',
                'excerpt' => 'A structured step-by-step path from selecting target roles to clearing screening aptitude tests, technical rounds, and corporate onboarding.',
                'display_order' => 1,
                'is_published' => true,
                'published_at' => now()->subDays(5),
                'featured_image' => 'images/placement/roadmap-overview.png',
                'featured_image_alt' => 'AptiMedha 4-Stage Placement Preparation Roadmap Overview',
                'meta_title' => 'Placement Preparation Roadmap: 4 Stages to Campus Success | AptiMedha',
                'meta_description' => 'Organize your placement prep with our structured 4-stage roadmap covering target roles, aptitude rounds, technical coding, and HR interviews.',
                'content' => '
<h2>Introduction to Placement Preparation</h2>
<p>Preparing for campus placements involves solving assessment questions, applying subject knowledge, communicating clearly, and presenting your project experience with confidence. This guide organizes those essential activities into a practical, repeatable roadmap.</p>
<p>Students, fresh graduates, and job seekers can adapt this roadmap to their current level, available preparation timeline, and specific employer assessment patterns.</p>

<h2>Stage 1: Choose Your Target Role & Evaluate Requirements</h2>
<p>Before diving into random aptitude practice or coding problems, identify the roles that match your academic qualification, strengths, and career aspirations.</p>
<table>
<thead>
<tr>
<th>Target Role</th>
<th>Core Preparation Focus</th>
</tr>
</thead>
<tbody>
<tr>
<td><strong>Software Developer</strong></td>
<td>Programming languages, Data Structures, Algorithms, OOP concepts, DBMS, and full-stack projects.</td>
</tr>
<tr>
<td><strong>Data Analyst</strong></td>
<td>Advanced Spreadsheets, SQL querying, Statistics, Data Interpretation, and exploratory projects.</td>
</tr>
<tr>
<td><strong>Quality Assurance & Testing</strong></td>
<td>Testing methodologies, test case design, SQL validation, debugging, and automation tools.</td>
</tr>
<tr>
<td><strong>Core Engineering</strong></td>
<td>Branch fundamentals (Mechanical, Civil, Electrical, Electronics), practical applications, and engineering projects.</td>
</tr>
<tr>
<td><strong>Business Consulting & Management</strong></td>
<td>Case assessments, commercial awareness, quantitative problem-solving, and communication skills.</td>
</tr>
</tbody>
</table>

<h2>Stage 2: Assess Your Starting Level & Diagnostic Testing</h2>
<p>Take an honest diagnostic assessment across three primary domains: Quantitative Aptitude, Logical Reasoning, and Verbal Ability. Use the diagnostic as a baseline planning tool:</p>
<ul>
<li><strong>Build the Foundation:</strong> Topics where concepts are unfamiliar or unclear. Focus on step-by-step conceptual review.</li>
<li><strong>Improve Through Practice:</strong> Topics where you understand the formula but make calculation errors or run out of time.</li>
<li><strong>Maintain Through Revision:</strong> Topics where you solve accurately within the time limit. Schedule periodic practice to retain speed.</li>
</ul>

<h2>Stage 3: Preparation Schedules & Daily Routines</h2>
<p>Select a preparation timeline that aligns with your upcoming recruitment drive:</p>
<ul>
<li><strong>60-Day Comprehensive Track:</strong> 2 hours daily. Weeks 1–4 dedicated to fundamental concepts and topic-wise practice; Weeks 5–6 dedicated to core technical subjects and coding; Weeks 7–8 focused on company papers and mock interviews.</li>
<li><strong>30-Day Intensive Track:</strong> 3.5 hours daily. High-frequency practice on recurring aptitude topics, speed math shortcuts, and top 25 behavioral questions.</li>
</ul>

<h2>Stage 4: Selection Process & Assessment Day Protocol</h2>
<p>Familiarize yourself with the typical recruitment pipeline:</p>
<ol>
<li>Online Screening: Quantitative aptitude, logical deduction, verbal comprehension, and optional coding MCQs.</li>
<li>Technical Round: Live coding, algorithmic complexity, architectural walkthrough of academic projects, and core CS concepts.</li>
<li>Group Discussion or Case Study: Demonstrating collaborative problem-solving, structured speaking, and respectful listening.</li>
<li>HR & Behavioral Interview: Cultural fit, communication clarity, ethics, and offer terms.</li>
</ol>
',
            ],
            [
                'title' => 'AptiMedha Resume Preparation Guide: Structure, ATS & Checklists',
                'slug' => 'resume-preparation-guide',
                'category' => 'resume',
                'excerpt' => 'A practical guide to single-page resume fundamentals for freshers and experienced candidates. Includes ATS optimization, STAR bullet points, and recruiter checklists.',
                'display_order' => 2,
                'is_published' => true,
                'published_at' => now()->subDays(4),
                'featured_image' => 'images/placement/resume-fundamentals-layout.png',
                'featured_image_alt' => 'AptiMedha Resume Preparation Layout and Structure Guide',
                'meta_title' => 'Resume Preparation Guide: Structure, STAR Method & ATS Rules | AptiMedha',
                'meta_description' => 'Learn how to format an ATS-compliant resume with quantifiable bullet points, clean section order, and verified recruiter checklists.',
                'content' => '
<h2>Purpose of a Professional Resume</h2>
<p>A resume is a concise document that presents your qualifications, skills, and relevant experience for a specific professional opportunity. Modern recruiters spend an average of fewer than 10 seconds on an initial resume screen. A clear layout ensures your strongest qualifications are immediately visible.</p>

<h2>Resume vs. CV vs. Biodata</h2>
<table>
<thead>
<tr>
<th>Feature</th>
<th>Resume</th>
<th>Curriculum Vitae (CV)</th>
<th>Biodata</th>
</tr>
</thead>
<tbody>
<tr>
<td><strong>Main Purpose</strong></td>
<td>Present relevant qualifications tailored to a specific job vacancy.</td>
<td>Provide a comprehensive academic, research, and publication record.</td>
<td>Provide personal background and demographic details in a fixed format.</td>
</tr>
<tr>
<td><strong>Typical Length</strong></td>
<td>1 page for undergraduates and freshers; 2 pages for experienced professionals.</td>
<td>Multiple pages as the candidate’s publication or academic record expands.</td>
<td>1 to 2 pages following prescribed organizational templates.</td>
</tr>
<tr>
<td><strong>Application Context</strong></td>
<td>Campus placements, corporate internships, and industry employment.</td>
<td>Academic positions, scientific fellowships, and doctoral applications.</td>
<td>Certain government bodies, administrative roles, or institutional forms.</td>
</tr>
</tbody>
</table>

<h2>Recommended Section Order for Undergraduates & Freshers</h2>
<ol>
<li><strong>Contact Header:</strong> Full name, professional email address, phone number, city/state, LinkedIn profile URL, and GitHub / portfolio link.</li>
<li><strong>Education:</strong> Degree, major, institution name, year of graduation, and CGPA / percentage.</li>
<li><strong>Technical Skills:</strong> Categorized by languages, developer tools, frameworks, and core subject proficiencies.</li>
<li><strong>Academic & Personal Projects:</strong> Highlight 2–3 significant projects using the STAR method (Situation, Task, Action, Result).</li>
<li><strong>Internships / Experience:</strong> Practical industry exposure, responsibilities, and quantified deliverables.</li>
<li><strong>Achievements & Certifications:</strong> Hackathon wins, competitive programming rankings, or recognized industry credentials.</li>
</ol>

<h2>Writing High-Impact Bullet Points (STAR Method)</h2>
<p>Avoid passive job duty descriptions like <em>"Responsible for creating a database"</em>. Instead, use strong action verbs and quantified outcomes:</p>
<blockquote>
<p><strong>Example:</strong> "Architected a distributed inventory tracking portal using Laravel and MySQL, reducing database query response times by 38% across 12,000 daily simulated transactions."</p>
</blockquote>

<h2>Recruiter’s Final Resume Checklist</h2>
<ul>
<li>Strict 1-page layout with clean margins and consistent font typography (10–12pt body text).</li>
<li>Action verbs at the beginning of each bullet point ("Engineered", "Designed", "Optimized", "Formulated").</li>
<li>Measurable metrics included for project achievements (percentages, user counts, load times).</li>
<li>Standard PDF export with selectable, machine-readable text for Applicant Tracking Systems (ATS).</li>
</ul>
',
            ],
            [
                'title' => 'Top HR Interview Questions & Behavioral Response Frameworks',
                'slug' => 'hr-interview-preparation-guide',
                'category' => 'interview',
                'excerpt' => 'Master common behavioral interview questions with structured response frameworks including "Tell me about yourself", handling weaknesses, and salary discussions.',
                'display_order' => 3,
                'is_published' => true,
                'published_at' => now()->subDays(3),
                'featured_image' => 'images/placement/roadmap-stages.png',
                'featured_image_alt' => 'HR Interview Behavioral Preparation Frameworks',
                'meta_title' => 'Top HR Interview Questions & Model Answers | AptiMedha',
                'meta_description' => 'Prepare for campus HR rounds with proven response frameworks for Tell Me About Yourself, strengths, weaknesses, and situational scenarios.',
                'content' => '
<h2>The Role of the HR Interview</h2>
<p>The HR round assesses whether you possess the professional maturity, communication competence, collaboration attitude, and cultural alignment necessary to thrive within the organization.</p>

<h2>Framework 1: "Tell Me About Yourself"</h2>
<p>Use the <strong>Present – Past – Future</strong> framework to structure your 90-second response:</p>
<ul>
<li><strong>Present:</strong> State your current degree, major, and primary area of technical or analytical focus.</li>
<li><strong>Past:</strong> Highlight 1–2 key project accomplishments, internships, or leadership responsibilities that illustrate your practical capability.</li>
<li><strong>Future:</strong> Express genuine enthusiasm for this specific role and explain how your skillset creates value for the company.</li>
</ul>

<h2>Framework 2: "What is Your Greatest Weakness?"</h2>
<p>Name a real, non-fatal professional attribute and describe the concrete, proactive steps you are taking to overcome it:</p>
<blockquote>
<p>"In the past, I tended to hesitate before delegating tasks during group project sprints because I wanted to verify every line of code myself. I realized this created bottlenecks, so during our final year capstone project, I instituted clear modular boundaries and daily standup check-ins, which improved our sprint velocity significantly."</p>
</blockquote>

<h2>Essential Guidelines for Interview Day</h2>
<ul>
<li>Research the organization: Familiarize yourself with their core service lines, industry verticals, recent milestones, and company values.</li>
<li>Prepare 2–3 thoughtful questions to ask the interviewer regarding team structure, training opportunities, or upcoming technical initiatives.</li>
<li>Maintain professional posture, steady eye contact, and attentive listening throughout.</li>
</ul>
',
            ],
            [
                'title' => 'Technical & Coding Interview Guide: DSA, Core CS and Project Defense',
                'slug' => 'technical-and-coding-interview-guide',
                'category' => 'technical',
                'excerpt' => 'Comprehensive technical interview roadmap covering Data Structures & Algorithms, DBMS, Operating Systems, Computer Networks, and defending academic projects.',
                'display_order' => 4,
                'is_published' => true,
                'published_at' => now()->subDays(2),
                'featured_image' => 'images/placement/roadmap-schedule.png',
                'featured_image_alt' => 'Technical and Coding Interview Preparation Roadmap',
                'meta_title' => 'Technical & Coding Interview Preparation Guide | AptiMedha',
                'meta_description' => 'Prepare for technical interviews with DSA fundamentals, DBMS indexing, OS process scheduling, and practical project explanation frameworks.',
                'content' => '
<h2>Three Pillars of Technical Interviews</h2>
<p>Technical assessments evaluate your foundational computer science knowledge, problem-solving methodology, and ability to articulate engineering trade-offs.</p>

<h2>1. Data Structures & Algorithmic Problem Solving</h2>
<p>Focus on foundational patterns rather than memorizing random solutions:</p>
<ul>
<li><strong>Arrays & Strings:</strong> Two-pointer techniques, sliding window patterns, prefix sums, and hashing.</li>
<li><strong>Linked Lists:</strong> Fast/slow pointers, in-place reversal, and cycle detection.</li>
<li><strong>Trees & Graphs:</strong> BFS/DFS traversals, binary search trees, and topological sorting.</li>
<li><strong>Dynamic Programming:</strong> Memoization and tabulation for knapsack and subsequence variations.</li>
</ul>

<h2>2. Core Computer Science Fundamentals</h2>
<ul>
<li><strong>DBMS & SQL:</strong> ACID properties, normalization vs. denormalization, primary vs. foreign keys, index structures (B-Trees), and JOIN queries.</li>
<li><strong>Operating Systems:</strong> Processes vs. threads, CPU scheduling algorithms, deadlock conditions and prevention, virtual memory, and paging.</li>
<li><strong>Computer Networks:</strong> OSI model layers, TCP vs. UDP protocols, DNS resolution workflow, HTTP/HTTPS handshake, and IP subnetting.</li>
</ul>

<h2>3. Defending Your Academic Projects</h2>
<p>Interviewers probe deeply into projects listed on your resume to verify individual ownership. Be prepared to answer:</p>
<ol>
<li>What motivated the project architecture and technology stack selection?</li>
<li>What was the most challenging technical bug encountered, and how did you diagnose and resolve it?</li>
<li>If given two more months, what performance or architectural improvements would you implement?</li>
</ol>
',
            ],
            [
                'title' => 'Aptitude Assessment Strategy: Speed Math, Logic & Verbal Tactics',
                'slug' => 'aptitude-assessment-preparation',
                'category' => 'aptitude',
                'excerpt' => 'Tactical preparation for online aptitude tests. Master speed math shortcuts, logical puzzle categorization, reading comprehension strategies, and error log management.',
                'display_order' => 5,
                'is_published' => true,
                'published_at' => now()->subDays(1),
                'featured_image' => null,
                'featured_image_alt' => null,
                'meta_title' => 'Aptitude Assessment Strategy & Speed Math Tactics | AptiMedha',
                'meta_description' => 'Master placement aptitude tests with time management tactics, speed calculation techniques, and systematic error tracking.',
                'content' => '
<h2>The Role of Aptitude Rounds in Campus Placements</h2>
<p>Online aptitude tests serve as the primary screening barrier for mass recruiters and product companies alike. The round is designed to test numerical aptitude, analytical problem-solving, and cognitive speed under strict time pressure.</p>

<h2>Time Allocation & Test Tactics</h2>
<ul>
<li><strong>The 60-Second Rule:</strong> If you cannot identify the formula or approach within the first 45–60 seconds, flag the question and move forward immediately. Do not get stuck on a single difficult puzzle.</li>
<li><strong>Speed Calculation Techniques:</strong> Memorize squares up to 30, cubes up to 15, fractional percentages (1/2 through 1/12), and Vedic math multiplication shortcuts.</li>
<li><strong>Negative Marking Mitigation:</strong> In assessments with negative marking, avoid uncalculated guesses. Eliminate at least two incorrect options before taking a calculated risk.</li>
</ul>

<h2>Maintaining an Error Log</h2>
<p>The fastest way to boost aptitude scores is systematic error analysis. For every practice test, log each incorrect answer:</p>
<ol>
<li>Conceptual Error: Formula was forgotten or misunderstood. (Action: Revise theory notes).</li>
<li>Calculation Error: Logic was correct but arithmetic was faulty. (Action: Slow down calculations).</li>
<li>Time Panic: Ran out of time and rushed. (Action: Improve question selection).</li>
</ol>
',
            ],
            [
                'title' => 'From Campus to Corporate: Job Offers, Verification & Professional Etiquette',
                'slug' => 'from-campus-to-corporate-guide',
                'category' => 'general',
                'excerpt' => 'Essential guidance on transitioning from college to corporate life. Understand CTC vs in-hand salary, employment bonds, document verification, and email etiquette.',
                'display_order' => 6,
                'is_published' => true,
                'published_at' => now(),
                'featured_image' => null,
                'featured_image_alt' => null,
                'meta_title' => 'From Campus to Corporate: Transition, CTC & Workplace Etiquette | AptiMedha',
                'meta_description' => 'Navigate the college to corporate transition with clear explanations of CTC components, background verification, and corporate etiquette.',
                'content' => '
<h2>Understanding Your Job Offer & Compensation</h2>
<p>Receiving a campus placement offer is an exciting milestone. Before accepting, evaluate the formal offer letter carefully:</p>
<ul>
<li><strong>Cost to Company (CTC) vs. In-Hand Salary:</strong> CTC includes basic pay, House Rent Allowance (HRA), employer provident fund (PF) contributions, gratuity, and performance bonuses. Your monthly take-home salary will be lower after tax deductions and statutory withholdings.</li>
<li><strong>Probation Period & Performance Criteria:</strong> Review the probationary duration (typically 3 to 6 months) and confirmation requirements.</li>
<li><strong>Service Agreements & Bonds:</strong> Understand any training cost recovery clauses or minimum service commitments before signing.</li>
</ul>

<h2>Background Verification & Document Checklist</h2>
<p>Maintain authenticated physical and digital copies of the following documents:</p>
<ol>
<li>All semester grade cards and provisional degree certificate.</li>
<li>Government photo identification (Aadhaar card, Passport, PAN card).</li>
<li>Formal internship completion letters and certificates.</li>
<li>Updated academic and personal contact information.</li>
</ol>
',
            ],
        ];

        foreach ($guides as $guideData) {
            PlacementGuide::updateOrCreate(
                ['slug' => $guideData['slug']],
                $guideData
            );
        }
    }
}

