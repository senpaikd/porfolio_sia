<?php

namespace Database\Seeders;

use App\Enums\ProjectStatus;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Project;
use App\Models\SiteSetting;
use App\Models\Skill;
use App\Models\Technology;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PortfolioContentSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $this->seedSiteSettings();
            $technologies = $this->seedTechnologies();
            $this->seedSkills();
            $this->seedExperience();
            $this->seedEducation();
            $this->seedProjects($technologies);
        });
    }

    private function seedSiteSettings(): void
    {
        SiteSetting::query()->updateOrCreate(['id' => 1], [
            'owner_name' => 'Kent C. Dayola',
            'professional_title' => 'Entry-level Web Developer',
            'biography' => 'Entry-level web developer with hands-on experience improving responsive interfaces, debugging existing code, testing web features, and building database-backed applications. Comfortable with HTML, CSS, JavaScript, React-based development, PHP/Laravel, MySQL, REST APIs, and Git/GitHub.',
            'email' => 'kentdayola@gmail.com',
            'github_url' => 'https://github.com/senpaikd',
            'linkedin_url' => null,
            'resume_path' => null,
            'availability' => 'Open to junior or freelance web development roles.',
            'seo_title' => 'Kent C. Dayola | Web Developer',
            'seo_description' => 'Portfolio of Kent C. Dayola, an entry-level web developer focused on responsive interfaces and database-backed applications.',
        ]);
    }

    /** @return array<string, Technology> */
    private function seedTechnologies(): array
    {
        $technologyData = [
            'Laravel' => 'Backend',
            'PHP' => 'Language',
            'MySQL' => 'Database',
            'Python' => 'Language',
            'AI Integration' => 'AI',
            'Next.js' => 'Framework',
            'OpenAI' => 'AI',
        ];

        $technologies = [];
        $displayOrder = 0;

        foreach ($technologyData as $name => $category) {
            $technologies[$name] = Technology::query()->updateOrCreate(['name' => $name], [
                'slug' => Str::slug($name),
                'category' => $category,
                'is_published' => true,
                'display_order' => $displayOrder++,
            ]);
        }

        return $technologies;
    }

    private function seedSkills(): void
    {
        Skill::query()->where('name', 'like', 'Placeholder:%')->delete();

        $skills = [
            'Frontend' => ['HTML5', 'CSS3', 'JavaScript', 'React / React Native', 'Responsive Web Design', 'Bootstrap'],
            'Backend / Data' => ['PHP', 'Laravel', 'MySQL', 'REST APIs', 'Python'],
            'Tools / Workflow' => ['Git', 'GitHub', 'Debugging', 'Cross-device Testing', 'Basic UI/UX Implementation'],
            'Other' => ['Problem-solving', 'Communication Skills', 'Bug Testing and Documentation', 'Responsive Interface Troubleshooting'],
        ];

        $displayOrder = 0;

        foreach ($skills as $category => $names) {
            foreach ($names as $name) {
                Skill::query()->updateOrCreate(['name' => $name], [
                    'category' => $category,
                    'proficiency' => null,
                    'icon' => null,
                    'is_published' => true,
                    'display_order' => $displayOrder++,
                ]);
            }
        }
    }

    private function seedExperience(): void
    {
        Experience::query()->where('company', 'Placeholder: Company')->delete();

        Experience::query()->updateOrCreate([
            'company' => 'Abenales.tech',
            'role' => 'Junior Front-End Developer',
        ], [
            'location' => null,
            'start_date' => '2026-09-01',
            'end_date' => '2026-10-31',
            'is_current' => false,
            'description' => "Contributed to an existing web and mobile codebase under a confidentiality agreement.\n- Refined responsive UI sections across multiple screen sizes.\n- Resolved spacing, alignment, navigation, and card-layout issues.\n- Tested local and deployed features, documented bugs, and applied feedback-based fixes.\n- Used Git and GitHub for branches, commits, merge coordination, and code handoff.",
            'is_published' => true,
            'display_order' => 0,
        ]);

        Experience::query()->updateOrCreate([
            'company' => 'KND Resource Management Corp. / Chemtrust Global Market Inc.',
            'role' => 'IT Support Intern (OJT)',
        ], [
            'location' => null,
            'start_date' => '2026-02-01',
            'end_date' => '2026-03-31',
            'is_current' => false,
            'description' => 'Provided basic computer, equipment, wiring, and connectivity troubleshooting in the Audit Department. Assisted with data encoding, ledger checking, receipt organization, and document verification.',
            'is_published' => true,
            'display_order' => 1,
        ]);
    }

    private function seedEducation(): void
    {
        Education::query()->where('institution', 'Placeholder: Institution')->delete();

        Education::query()->updateOrCreate([
            'institution' => 'Southern Leyte State University - Bontoc Campus',
            'qualification' => 'Bachelor of Science in Information Technology',
        ], [
            'field' => 'Information Technology',
            'start_date' => null,
            'end_date' => null,
            'description' => 'Graduation year: 2026.',
            'is_published' => true,
            'display_order' => 0,
        ]);
    }

    /** @param array<string, Technology> $technologies */
    private function seedProjects(array $technologies): void
    {
        $docuMind = Project::query()->updateOrCreate(['slug' => 'documind'], [
            'title' => 'DocuMind',
            'short_description' => 'A privacy-conscious AI document assistant with local passage ranking, traceable citations, and source-grounded answers.',
            'full_description' => 'DocuMind lets users upload PDF, TXT, and Markdown files and ask questions grounded in their contents. Document parsing, chunking, and relevance ranking happen in the browser. Only the question, recent conversation, and a small set of relevant text passages are sent for AI processing, and answers include traceable source citations. Files are session-only, and no account is required.',
            'status' => ProjectStatus::InDevelopment,
            'is_featured' => true,
            'is_published' => true,
            'display_order' => 0,
            'github_url' => null,
            'live_url' => 'https://documind-bay-kappa.vercel.app/',
            'seo_title' => 'DocuMind | AI Document Assistant',
            'seo_description' => 'A privacy-conscious AI document assistant with local passage ranking, source-grounded answers, and traceable citations.',
        ]);

        $docuMind->technologies()->syncWithoutDetaching(
            collect(['Next.js', 'OpenAI', 'AI Integration'])
                ->map(fn (string $name): int => $technologies[$name]->id)
                ->all(),
        );

        $helpDesk = Project::query()->updateOrCreate(['slug' => 'helpdesk-ticketing-system'], [
            'title' => 'HelpDesk Ticketing System',
            'short_description' => 'A secure Laravel support portal for submitting, tracking, and continuing support requests.',
            'full_description' => 'HelpDesk is a Laravel support portal with secure account registration and sign-in. Registered users can submit and follow support requests, continue ticket conversations, and access their support history. The public interface includes password recovery, a 30-day keep-me-signed-in option, and a light/dark theme.',
            'status' => ProjectStatus::Completed,
            'is_featured' => true,
            'is_published' => true,
            'display_order' => 1,
            'completion_date' => null,
            'github_url' => null,
            'live_url' => 'https://helpdesk-ticketing.42web.io/',
            'seo_title' => 'HelpDesk Ticketing System | Kent C. Dayola',
            'seo_description' => 'A secure Laravel support portal for submitting, tracking, and continuing support requests and conversations.',
        ]);

        $helpDesk->technologies()->syncWithoutDetaching([$technologies['Laravel']->id]);

        $seawiseHub = Project::query()->updateOrCreate(['slug' => 'seawisehub'], [
            'title' => 'SeawiseHub',
            'short_description' => 'A full-stack plankton detection and classification system built as a capstone project.',
            'full_description' => 'Built a full-stack web application using Laravel, PHP, MySQL, Python, and AI integration for plankton detection and classification. Integrated a machine-learning model for automated species classification and connected the workflow to a database-backed web system.',
            'status' => ProjectStatus::InDevelopment,
            'is_featured' => false,
            'is_published' => false,
            'display_order' => 2,
            'completion_date' => null,
            'github_url' => null,
            'live_url' => null,
            'seo_title' => 'SeawiseHub | Kent C. Dayola',
            'seo_description' => 'SeawiseHub is a Laravel and Python capstone project for AI-assisted plankton detection and classification.',
        ]);

        $seawiseHub->technologies()->syncWithoutDetaching(
            collect(['Laravel', 'PHP', 'MySQL', 'Python', 'AI Integration'])
                ->map(fn (string $name): int => $technologies[$name]->id)
                ->all(),
        );
    }
}
