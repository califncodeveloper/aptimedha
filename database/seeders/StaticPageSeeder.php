<?php

namespace Database\Seeders;

use App\Models\StaticPage;
use Illuminate\Database\Seeder;

class StaticPageSeeder extends Seeder
{
    /**
     * Seed static pages using approved client content.
     */
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'about',
                'title' => 'About AptiMedha',
                'meta_title' => 'About Us - AptiMedha: Practise Smart. Get Placement Ready.',
                'meta_description' => 'AptiMedha is an educational platform that helps students, graduates and job seekers strengthen their knowledge, develop problem-solving skills and prepare for placement assessments and interviews.',
                'is_active' => true,
                'content' => '<h2>Practise Smart. Get Placement Ready.</h2>
<p>AptiMedha is an educational platform that helps students, graduates and job seekers strengthen their knowledge, develop problem-solving skills and prepare for placement assessments and interviews.</p>
<p>We bring aptitude practice, technical learning and career preparation together in one place, helping learners move through their preparation with greater clarity and confidence.</p>
<p>Our name combines "Apti," inspired by aptitude, and "Medha," a Sanskrit word associated with intelligence and understanding. It reflects our belief that effective preparation involves both solving problems and understanding the concepts behind them.</p>

<h3>Our Mission</h3>
<p>To make placement preparation accessible, understandable and purposeful.</p>
<p>We aim to support learners through organised practice questions, clear explanations and practical guidance. Our mission is to help students build strong foundations, learn from their mistakes and develop the skills needed to approach assessments and interviews with confidence.</p>

<h3>Our Vision</h3>
<p>To become a trusted learning companion that helps students turn preparation into opportunity.</p>
<p>We envision a platform where learners from different educational backgrounds can find a clear starting point, build their abilities at their own pace and prepare thoughtfully for the next stage of their careers.</p>

<h3>Our Approach</h3>
<ul>
    <li><strong>Build understanding through practice:</strong> We believe a question should offer more than a score. Our approach pairs practice with explanations that help learners understand methods, recognise mistakes and apply what they learn to new problems.</li>
    <li><strong>Give learning a clear structure:</strong> We organise resources by subject and topic so learners can start with the fundamentals, focus on areas that need attention and progress towards more challenging material.</li>
    <li><strong>Connect knowledge with career skills:</strong> Placement preparation extends beyond answering MCQs. Our resources also address communicating ideas, explaining projects, participating in group discussions and presenting qualifications through a professional resume.</li>
    <li><strong>Encourage independent thinking:</strong> Sample answers and templates provide guidance. We encourage learners to adapt them to their own experiences, explain their reasoning and develop an authentic voice.</li>
    <li><strong>Improve through feedback:</strong> Useful educational content requires ongoing review. We welcome corrections and suggestions that help us improve accuracy, clarity and the learning experience.</li>
</ul>

<h3>What We Cover</h3>
<p>AptiMedha’s learning areas include:</p>
<ul>
    <li>Arithmetic Aptitude and Data Interpretation</li>
    <li>Logical, Verbal and Non-Verbal Reasoning</li>
    <li>English and Verbal Ability</li>
    <li>General Knowledge and Current Affairs</li>
    <li>Programming and Engineering</li>
    <li>Selected Graduate and Postgraduate Subjects</li>
    <li>Placement Preparation, including HR interviews, technical interviews, group discussions, practice papers and resume guidance</li>
</ul>

<h3>Our Commitment to Learners</h3>
<p>We aim to provide clear content, honest guidance and a convenient learning experience across devices. We distinguish original practice resources from official information and encourage learners to verify changing recruitment requirements with the relevant organisation.</p>
<p>We value steady progress. Understanding one concept, correcting one mistake and practising consistently can each contribute to stronger preparation.</p>
<p><strong>AptiMedha is an independent educational platform.</strong> We are not affiliated with employers or examination authorities unless expressly stated. Our resources support preparation and do not guarantee examination results, selection or employment.</p>',
            ],
            [
                'slug' => 'contact',
                'title' => 'Contact Us',
                'meta_title' => 'Contact Us - AptiMedha Support & Feedback',
                'meta_description' => 'Have a question, found an incorrect answer or want to suggest a topic? Get in touch with the AptiMedha editorial and support team.',
                'is_active' => true,
                'content' => '<h2>We’re Here to Help You Prepare Better</h2>
<p>Have a question, found an incorrect answer or want to suggest a topic? We welcome your feedback.</p>
<p>At AptiMedha, your observations help us improve our questions, explanations and learning experience. Whether you need help using the website or want to share an idea, please get in touch.</p>

<h3>How Can We Help?</h3>
<ul>
    <li><strong>Report a Question or Answer Error:</strong> If a question, option or explanation appears incorrect, share the page link, question number and your suggested correction. Supporting calculations or references will help us review it.</li>
    <li><strong>Suggest a Topic:</strong> Tell us which subject, chapter or placement preparation resource you would like us to cover. We consider suggestions when planning new content.</li>
    <li><strong>Get Technical Support:</strong> Having trouble opening a page, viewing an image or downloading a template? Describe the issue and tell us which device and browser you are using.</li>
    <li><strong>Share General Feedback:</strong> Let us know what works well and what could be clearer. Suggestions about navigation, readability and accessibility are welcome.</li>
    <li><strong>Discuss Collaboration:</strong> For educational collaborations, content licensing or business enquiries, send a brief introduction and explain your proposal.</li>
    <li><strong>Raise a Copyright or Privacy Concern:</strong> For ownership concerns, include the relevant page URL and supporting details. For privacy enquiries, explain your request without sending unnecessary identity documents or sensitive information.</li>
</ul>

<h3>Official Contact Desk</h3>
<p><strong>Email:</strong> aptimedha@gmail.com</p>
<p><strong>Operating Hours:</strong> Monday – Friday, 9:00 AM – 6:00 PM IST</p>
<p><em>Please note: We will use the details you provide to respond to your enquiry, as explained in our Privacy Policy. Please do not include passwords, Aadhaar numbers, bank details or other sensitive information.</em></p>',
            ],
            [
                'slug' => 'disclaimer',
                'title' => 'Website Disclaimer',
                'meta_title' => 'Website Disclaimer - AptiMedha',
                'meta_description' => 'Educational purpose, accuracy, no guarantee of results, and independent platform disclaimer for AptiMedha.',
                'is_active' => true,
                'content' => '<p class="text-xs text-gray-500">Effective date: 26/10/2026 | Last updated: 26/10/2026</p>

<h3>1. Educational Purpose</h3>
<p>AptiMedha provides questions, explanations, articles, sample answers, resume guidance and related resources for educational and practice purposes.</p>
<p>The content is intended to support independent learning. It does not replace official recruitment notices, examination instructions, prescribed syllabi or professional advice.</p>

<h3>2. Accuracy and Updates</h3>
<p>We aim to make our content accurate and useful. However, errors, omissions and outdated information may occur.</p>
<p>Answers may depend on assumptions, software versions or the context stated in a question. Recruitment processes, eligibility conditions and assessment patterns may also change.</p>
<p>Verify important information with the relevant employer, institution, examination authority or official documentation before acting on it.</p>

<h3>3. No Guarantee of Results</h3>
<p>Using AptiMedha does not guarantee a particular score, examination result, interview call, placement, salary or job offer.</p>
<p>Outcomes depend on factors such as individual preparation, eligibility, assessment performance and employer requirements.</p>

<h3>4. Independent Platform</h3>
<p>AptiMedha is not affiliated with or endorsed by any employer, university, examination authority or other organisation unless expressly stated.</p>
<p>Company names, product names and trademarks belong to their respective owners. References are used for identification and educational context.</p>

<h3>5. Placement Papers and Practice Material</h3>
<p>Original practice sets are preparation resources. They should not be treated as actual company papers or previous-year questions unless expressly identified and supported by a verified source.</p>
<p>Candidate-recalled material, where published, may be incomplete and may not reflect a current assessment.</p>

<h3>6. Interview, GD and Resume Guidance</h3>
<p>Interview answers, discussion viewpoints and resume examples are illustrative. Users should adapt them to their own circumstances and include only truthful information. A sample answer or resume template does not guarantee selection.</p>

<h3>7. External Links and Advertisements</h3>
<p>Links to third-party websites are provided for convenience. We do not control their content, services, security or privacy practices. Advertisements or sponsored placements, where displayed, do not automatically constitute an endorsement.</p>

<h3>8. Reporting Concerns</h3>
<p>To report an error, ownership concern or misleading statement, contact <strong>aptimedha@gmail.com</strong> with the relevant page URL and supporting details.</p>',
            ],
            [
                'slug' => 'terms-and-conditions',
                'title' => 'Terms and Conditions',
                'meta_title' => 'Terms and Conditions - AptiMedha',
                'meta_description' => 'Terms governing access to and use of AptiMedha, educational resources, intellectual property, and acceptable user conduct.',
                'is_active' => true,
                'content' => '<p class="text-xs text-gray-500">Effective date: 26/10/2026 | Last updated: 26/10/2026</p>

<h3>1. Introduction</h3>
<p>These Terms govern access to and use of AptiMedha. By using the website, you agree to these Terms. If you do not agree, please stop using it.</p>
<p>Our Privacy Policy separately explains how personal information is handled. Acceptance of these Terms does not replace any separate consent required for personal-data processing.</p>

<h3>2. Purpose of the Website</h3>
<p>AptiMedha provides educational resources, including MCQs, explanations, interview preparation, discussion topics, resume guidance and practice material. Content and features may be added, revised or discontinued as the platform develops.</p>

<h3>3. Acceptable Use</h3>
<p>You may use the website for lawful learning and preparation. You must not:</p>
<ul>
    <li>Attempt unauthorised access to the website, its administration panel or systems.</li>
    <li>Introduce malware or interfere with website availability.</li>
    <li>Bypass security controls or access restrictions.</li>
    <li>Use automated collection methods that bypass controls, overload the service or infringe rights.</li>
    <li>Impersonate another person or submit misleading information.</li>
    <li>Use the platform to facilitate cheating in live assessments.</li>
    <li>Submit unlawful, threatening, defamatory or infringing material.</li>
</ul>

<h3>4. Intellectual Property</h3>
<p>Unless otherwise identified, AptiMedha’s original text, questions, explanations, designs and downloadable materials are owned by or licensed to AptiMedha.</p>
<p>You may view content and use materials made available for download for personal, non-commercial preparation. You may share links to our pages. Republishing substantial content, redistributing question banks, selling our materials or removing ownership notices requires permission, except where permitted by law.</p>

<h3>5. Content and Practice Results</h3>
<p>Practice scores and sample answers are learning aids. They are not formal qualifications, employment assessments or certifications unless expressly stated otherwise.</p>

<h3>6. Feedback and Submitted Material</h3>
<p>If you send corrections or suggestions, we may use them to improve the website. You should submit only material you have the right to share.</p>

<h3>7. Limitation of Liability</h3>
<p>To the extent permitted by applicable law, AptiMedha and its operator are not liable for indirect or consequential losses arising from use of the website.</p>

<h3>8. Governing Law and Disputes</h3>
<p>These Terms are governed by the laws of India.</p>',
            ],
            [
                'slug' => 'privacy-policy',
                'title' => 'Privacy Policy',
                'meta_title' => 'Privacy Policy - AptiMedha',
                'meta_description' => 'Information on personal data collection, cookies, technical logs, and student privacy rights on AptiMedha.',
                'is_active' => true,
                'content' => '<p class="text-xs text-gray-500">Effective date: 26/10/2026 | Last updated: 26/10/2026</p>

<h3>1. Who We Are</h3>
<p>This Privacy Policy explains what personal information we collect through AptiMedha, why we use it, who may receive it and how you can contact us about it.</p>
<p><strong>Privacy Contact:</strong> aptimedha@gmail.com</p>

<h3>2. Information We Collect</h3>
<p><strong>Information you provide:</strong> When you contact us or report an error, we collect the information you submit, including your name, email address, message and correspondence, and relevant page links.</p>
<p>Please do not submit identity documents, financial details or other sensitive information unless we specifically explain why it is necessary and provide an appropriate collection method.</p>
<p><strong>Technical information:</strong> Our website and hosting services may process IP addresses, browser and device information, pages requested, and error logs for security and reliability.</p>

<h3>3. Why We Use Information</h3>
<ul>
    <li>Respond to enquiries and correction requests.</li>
    <li>Operate and maintain the website.</li>
    <li>Identify errors and improve usability.</li>
    <li>Detect abuse and protect the website.</li>
    <li>Meet applicable legal requirements.</li>
</ul>
<p>We do not treat a support enquiry as permission to send marketing messages. We do not sell enquiry information to recruiters or other organisations for their marketing purposes.</p>

<h3>4. Cookies and Security</h3>
<p>We use session cookies necessary for authenticated account actions and CSRF security. We use reasonable safeguards appropriate to the information processed, including HTTPS and restricted administrative access.</p>

<h3>5. Your Choices and Requests</h3>
<p>You may contact us to ask about your personal information or request correction or deletion at <strong>aptimedha@gmail.com</strong>.</p>',
            ],
            [
                'slug' => 'copyright',
                'title' => 'Copyright Policy',
                'meta_title' => 'Copyright Policy - AptiMedha',
                'meta_description' => 'Respecting original work, learning use guidelines, and copyright reporting procedures on AptiMedha.',
                'is_active' => true,
                'content' => '<p class="text-xs text-gray-500">Last updated: 26/10/2026</p>

<h3>Respecting Original Work. Supporting Responsible Learning.</h3>
<p>AptiMedha respects intellectual property and encourages the responsible use of educational resources. This policy explains how content on our website may be used and how to report an ownership concern.</p>

<h3>Ownership of Content</h3>
<p>Unless otherwise stated, copyright in the original content published on AptiMedha belongs to AptiMedha or its licensors. This may include original questions, explanations, articles, illustrations, graphics, resume templates and the original selection and arrangement of learning materials.</p>
<p>Third-party content remains the property of its respective owners. Copyright protection applies to eligible original expression; we do not claim exclusive ownership of general facts, ideas, concepts or methods.</p>

<h3>Personal Learning Use</h3>
<p>You may browse our pages and use materials expressly offered for download for your personal, non-commercial preparation. You are welcome to share links to AptiMedha pages with classmates, teachers and other learners.</p>

<h3>Uses Requiring Permission</h3>
<p>Except where permitted by law or an express licence, prior written permission is required to:</p>
<ul>
    <li>Republish substantial portions of our content on another website, application or publication.</li>
    <li>Copy or redistribute our question banks, explanations or downloadable resources.</li>
    <li>Sell, sublicense or commercially distribute our materials.</li>
    <li>Incorporate our content into a commercial course, product or service.</li>
    <li>Remove copyright notices or present our original work as your own.</li>
</ul>

<h3>Reporting a Copyright Concern</h3>
<p>If you believe material on AptiMedha infringes your copyright, please send your details, identification of the work, the exact URL, and supporting documentation to <strong>aptimedha@gmail.com</strong>.</p>',
            ],
        ];

        foreach ($pages as $page) {
            StaticPage::updateOrCreate(
                ['slug' => $page['slug']],
                $page
            );
        }
    }
}

