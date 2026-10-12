# Developer Portfolio and Admin Dashboard Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a responsive public developer portfolio and a secure Filament administration panel backed by MySQL.

**Architecture:** Public Blade controllers and components read published portfolio content from focused Eloquent models. Filament resources manage the same models behind administrator-only panel access, while dedicated services own image processing and deletion. Feature tests pin public visibility, authorization, validation, uploads, contact throttling, and SEO behavior.

**Tech Stack:** Laravel 12.69.3, PHP 8.2, MySQL, Blade, Tailwind CSS 4.1+, Vite 7, Filament 5, Livewire 4, Intervention Image 3 with GD, PHPUnit 11, Laravel Pint.

**Spec:** `docs/superpowers/specs/2026-10-06-developer-portfolio-design.md`

## Global Constraints

- Use Laravel 12.69.3 and PHP 8.2-compatible packages; install Filament `~5.0` for PowerShell compatibility.
- Use MySQL for local and production configuration while retaining SQLite `:memory:` for isolated automated tests.
- Do not invent biography, employment, education, achievements, testimonials, or project facts.
- Seed DocuMind only as an unpublished `In development` placeholder with no repository or demo URL.
- Permit external links only for normalized HTTP or HTTPS URLs and render new-tab links with `rel="noopener noreferrer"`.
- Store project media on Laravel's `public` disk with generated filenames; never trust client filenames.
- Keep public registration disabled and require `users.is_admin = true` for Filament panel access.
- Keep JavaScript limited to navigation and theme behavior; honor reduced motion and accessibility requirements.
- Never commit passwords or secrets; development administrator values come from environment variables.
- The directory is not currently a Git repository. Commit steps are recorded for a future initialized repository but must be skipped rather than initializing Git without user authorization.

## Review Focus

- Unknown, draft, or newly unpublished slugs must return 404 and must not leak into cards, metadata, or the sitemap; Tasks 3 and 9 own the tests.
- Duplicate or punctuation-only project titles must not create invalid or colliding slugs; Tasks 2 and 7 own the tests.
- `javascript:`, `data:`, malformed, and non-HTTP(S) links must be rejected while blank optional links remain valid; Task 2 owns the tests.
- Image replacement and deletion must not remove unrelated files and must tolerate already-missing files; Task 6 owns the tests.
- Contact throttling must isolate unrelated visitors and reject only excess submissions inside the configured window; Task 5 owns the tests.

---

### Task 1: Platform, MySQL, and Administrator Access Foundation

**Files:**
- Modify: `composer.json`
- Modify: `composer.lock`
- Modify: `package.json`
- Create: `package-lock.json`
- Modify: `.env`
- Modify: `.env.example`
- Create: `app/Providers/Filament/AdminPanelProvider.php`
- Modify: `bootstrap/providers.php`
- Create: `database/migrations/2026_10_06_000000_add_is_admin_to_users_table.php`
- Modify: `app/Models/User.php`
- Create: `database/seeders/AdminUserSeeder.php`
- Modify: `database/seeders/DatabaseSeeder.php`
- Create: `tests/Feature/Admin/PanelAccessTest.php`

**Interfaces:**
- Consumes: Laravel's existing `User` model and authentication guard.
- Produces: `User::canAccessPanel(Panel $panel): bool`, administrator flag, `/admin` panel, and environment-driven administrator seeding.

- [ ] **Step 1: Write failing panel-access tests**

Add tests named `guest_is_redirected_from_admin_panel`, `non_admin_user_is_forbidden_from_admin_panel`, and `administrator_can_open_admin_panel`; assert redirect, 403, and 200 respectively.

- [ ] **Step 2: Run the focused tests and verify failure**

Run: `php artisan test tests/Feature/Admin/PanelAccessTest.php`

Expected: FAIL because the panel and `is_admin` column do not exist.

- [ ] **Step 3: Install compatible dependencies and panel scaffolding**

Run `composer require filament/filament:"~5.0" intervention/image-laravel`, then `php artisan filament:install --panels`, then `npm install`. Preserve the existing Vite and Tailwind setup and verify Filament registers its provider.

- [ ] **Step 4: Configure MySQL and safe administrator environment values**

Set the local `.env` database driver to `mysql` with Laragon defaults (`127.0.0.1:3306`, database `portfolio`, username `root`, blank password unless the local installation differs). Set `FILESYSTEM_DISK=public`. Mirror keys in `.env.example` with blank `ADMIN_PASSWORD` and documented placeholder values for `ADMIN_NAME` and `ADMIN_EMAIL`.

- [ ] **Step 5: Implement administrator authorization and seeding**

Add the indexed `is_admin` boolean migration. Implement `FilamentUser` and `canAccessPanel()` on `User`. Implement `AdminUserSeeder::run(): void` so it creates or updates an administrator only when all required environment values exist and never supplies a hard-coded password.

- [ ] **Step 6: Run migrations and focused tests**

Run: `php artisan migrate` and `php artisan test tests/Feature/Admin/PanelAccessTest.php`

Expected: migrations complete and all three tests PASS.

- [ ] **Step 7: Commit the foundation when Git is available**

Commit message: `feat: add secure Filament admin foundation`

### Task 2: Portfolio Schema, Models, Factories, and Placeholder Seeds

**Files:**
- Create: `database/migrations/2026_10_06_010000_create_portfolio_content_tables.php`
- Create: `app/Enums/ProjectStatus.php`
- Create: `app/Models/Project.php`
- Create: `app/Models/ProjectImage.php`
- Create: `app/Models/Technology.php`
- Create: `app/Models/Skill.php`
- Create: `app/Models/Experience.php`
- Create: `app/Models/Education.php`
- Create: `app/Models/SiteSetting.php`
- Create: `app/Models/ContactMessage.php`
- Create: `app/Casts/NormalizedHttpUrl.php`
- Create: `database/factories/ProjectFactory.php`
- Create: `database/factories/TechnologyFactory.php`
- Create: `database/factories/SkillFactory.php`
- Create: `database/factories/ExperienceFactory.php`
- Create: `database/factories/EducationFactory.php`
- Create: `database/factories/SiteSettingFactory.php`
- Create: `database/factories/ContactMessageFactory.php`
- Create: `database/seeders/PortfolioContentSeeder.php`
- Modify: `database/seeders/DatabaseSeeder.php`
- Create: `tests/Feature/Models/PortfolioModelTest.php`
- Create: `tests/Unit/NormalizedHttpUrlTest.php`

**Interfaces:**
- Consumes: Eloquent, MySQL schema builder, and factory infrastructure.
- Produces: `Project::published()`, `Project::featured()`, `Project::ordered()`, `Project::getRouteKeyName(): string`, model relationships, `ProjectStatus`, and `NormalizedHttpUrl`.

- [ ] **Step 1: Write failing schema and relationship tests**

Test all relationships, casts, cascading pivot/image rows, default ordering, published/featured scopes, slug route key, singleton setting behavior, and exact DocuMind seed constraints.

- [ ] **Step 2: Write failing URL normalization tests**

Assert blank optional URLs remain null, host casing is normalized, HTTP(S) URLs pass, and `javascript:`, `data:`, FTP, malformed, and credential-bearing URLs throw validation-compatible exceptions.

- [ ] **Step 3: Run model and URL tests and verify failure**

Run: `php artisan test tests/Feature/Models/PortfolioModelTest.php tests/Unit/NormalizedHttpUrlTest.php`

Expected: FAIL because the schema and model interfaces do not exist.

- [ ] **Step 4: Create the normalized schema and indexes**

Create projects, project images, technologies, project-technology pivot, skills, experiences, education, site settings, and contact messages. Add unique slugs and compound indexes for `(is_published, display_order)`, `(is_published, is_featured, display_order)`, status, screenshot ordering, and pivot uniqueness.

- [ ] **Step 5: Implement enums, casts, models, scopes, and relationships**

Use typed casts for dates and booleans. Generate candidate slugs from non-empty titles, require an explicit valid slug when generation produces an empty value, and suffix collisions deterministically. Apply URL normalization to repository, demo, and social fields.

- [ ] **Step 6: Implement factories and clearly labeled placeholder seeds**

Seed one site-setting record and placeholder skills, technologies, experience, education, and DocuMind. Prefix invented development content with `Placeholder:` or equivalent visible labels.

- [ ] **Step 7: Run the focused tests**

Run: `php artisan test tests/Feature/Models/PortfolioModelTest.php tests/Unit/NormalizedHttpUrlTest.php`

Expected: PASS.

- [ ] **Step 8: Commit the domain model when Git is available**

Commit message: `feat: add portfolio content model`

### Task 3: Public Queries, Controllers, and Routes

**Files:**
- Create: `app/Http/Controllers/HomeController.php`
- Create: `app/Http/Controllers/ProjectController.php`
- Create: `app/View/Models/HomePageData.php`
- Modify: `routes/web.php`
- Create: `tests/Feature/PublicPortfolioTest.php`
- Create: `tests/Feature/ProjectVisibilityTest.php`

**Interfaces:**
- Consumes: model scopes from Task 2.
- Produces: named routes `home`, `projects.index`, and `projects.show`; `HomeController::__invoke(): View`; `ProjectController::index(): View`; `ProjectController::show(Project $project): View`.

- [ ] **Step 1: Write failing public-route tests**

Assert homepage and gallery return 200, gallery paginates, featured items require both flags, published items respect display order, draft items never appear, valid published slugs resolve, and missing/draft/newly-unpublished slugs return 404.

- [ ] **Step 2: Run the focused tests and verify failure**

Run: `php artisan test tests/Feature/PublicPortfolioTest.php tests/Feature/ProjectVisibilityTest.php`

Expected: FAIL because routes and controllers are absent.

- [ ] **Step 3: Implement named routes and focused controllers**

Use eager loading for technologies and ordered images. Resolve project bindings through a published-only route binding or an explicit controller guard so unpublished records always return 404.

- [ ] **Step 4: Run the focused tests**

Run: `php artisan test tests/Feature/PublicPortfolioTest.php tests/Feature/ProjectVisibilityTest.php`

Expected: PASS.

- [ ] **Step 5: Commit the public data flow when Git is available**

Commit message: `feat: add public portfolio routes and queries`

### Task 4: Responsive Blade Portfolio and Theme

**Files:**
- Create: `resources/views/components/layouts/app.blade.php`
- Create: `resources/views/components/site/navigation.blade.php`
- Create: `resources/views/components/site/section-heading.blade.php`
- Create: `resources/views/components/site/project-card.blade.php`
- Create: `resources/views/components/site/technology-badge.blade.php`
- Create: `resources/views/components/site/empty-state.blade.php`
- Create: `resources/views/components/site/footer.blade.php`
- Create: `resources/views/home.blade.php`
- Create: `resources/views/projects/index.blade.php`
- Create: `resources/views/projects/show.blade.php`
- Create: `resources/views/errors/404.blade.php`
- Modify: `resources/css/app.css`
- Modify: `resources/js/app.js`
- Delete: `resources/views/welcome.blade.php`
- Create: `tests/Feature/PublicMarkupTest.php`

**Interfaces:**
- Consumes: named routes and view data from Task 3.
- Produces: accessible responsive public UI, reusable project components, and persistent system-aware light/dark theme behavior.

- [ ] **Step 1: Write failing semantic markup tests**

Assert page title and landmarks, one primary heading, navigation labels, safe external-link attributes, featured and empty states, contact-section anchor, theme control, image alternative text, and project pagination markup.

- [ ] **Step 2: Run markup tests and verify failure**

Run: `php artisan test tests/Feature/PublicMarkupTest.php`

Expected: FAIL because the portfolio views do not exist.

- [ ] **Step 3: Build the shared layout and component system**

Implement the approved editorial visual direction with warm neutrals, a restrained accent, typography hierarchy, visible focus states, responsive spacing, and semantic components.

- [ ] **Step 4: Build homepage, gallery, detail, and 404 views**

Render only supplied data and visibly label seeded placeholders. Include useful empty states and responsive image dimensions. Do not add invented testimonials, metrics, or biography.

- [ ] **Step 5: Add minimal theme and navigation behavior**

Implement a local-storage theme preference with system fallback and an accessible mobile-navigation toggle. Disable nonessential transitions under `prefers-reduced-motion`.

- [ ] **Step 6: Run markup and existing public tests**

Run: `php artisan test tests/Feature/PublicMarkupTest.php tests/Feature/PublicPortfolioTest.php tests/Feature/ProjectVisibilityTest.php`

Expected: PASS.

- [ ] **Step 7: Commit the public experience when Git is available**

Commit message: `feat: build responsive portfolio interface`

### Task 5: Contact Form, Persistence, and Rate Limiting

**Files:**
- Create: `app/Http/Requests/StoreContactMessageRequest.php`
- Create: `app/Http/Controllers/ContactMessageController.php`
- Modify: `app/Providers/AppServiceProvider.php`
- Modify: `routes/web.php`
- Create: `resources/views/components/site/contact-form.blade.php`
- Modify: `resources/views/home.blade.php`
- Create: `tests/Feature/ContactFormTest.php`

**Interfaces:**
- Consumes: `ContactMessage` from Task 2.
- Produces: named route `contact.store`, request validation, and limiter key derived from normalized email plus client IP.

- [ ] **Step 1: Write failing contact-flow tests**

Assert valid persistence and success feedback; required fields, email format, subject/message lengths, honeypot behavior, CSRF middleware presence, per-visitor rate limiting, and independence of distinct visitor keys.

- [ ] **Step 2: Run contact tests and verify failure**

Run: `php artisan test tests/Feature/ContactFormTest.php`

Expected: FAIL because the form endpoint is absent.

- [ ] **Step 3: Implement request validation and throttled persistence**

Define `StoreContactMessageRequest::rules(): array`, register a named rate limiter, and implement `ContactMessageController::store(StoreContactMessageRequest $request): RedirectResponse`.

- [ ] **Step 4: Add the accessible contact form**

Render labels, autocomplete values, field errors, retained safe input, honeypot, status feedback, and a non-JavaScript submission flow.

- [ ] **Step 5: Run contact tests**

Run: `php artisan test tests/Feature/ContactFormTest.php`

Expected: PASS.

- [ ] **Step 6: Commit the contact flow when Git is available**

Commit message: `feat: add protected portfolio contact form`

### Task 6: Image Processing and Safe File Lifecycle

**Files:**
- Create: `app/Services/ProjectImageService.php`
- Create: `app/Observers/ProjectObserver.php`
- Create: `app/Observers/ProjectImageObserver.php`
- Modify: `app/Providers/AppServiceProvider.php`
- Create: `config/image.php`
- Create: `tests/Feature/ProjectImageServiceTest.php`

**Interfaces:**
- Consumes: `UploadedFile`, Laravel `public` disk, Intervention Image with GD, `Project`, and `ProjectImage`.
- Produces: `ProjectImageService::store(UploadedFile $file, string $directory): string`, `ProjectImageService::delete(?string $path): void`, WebP output capped to documented display dimensions, and observer-driven obsolete-file cleanup.

- [ ] **Step 1: Write failing storage lifecycle tests**

Using `Storage::fake('public')`, assert generated filenames, WebP encoding, dimension bounding, replacement cleanup after persistence, project/screenshot deletion cleanup, missing-file tolerance, and protection against deleting an unrelated file path.

- [ ] **Step 2: Run image tests and verify failure**

Run: `php artisan test tests/Feature/ProjectImageServiceTest.php`

Expected: FAIL because the service and observers are absent.

- [ ] **Step 3: Implement processing and deletion service**

Use GD with auto-orientation, strip metadata, bound main images to 1600 pixels and screenshots to 1920 pixels on their longest edge, encode WebP at quality 82, and save under generated UUID-based names within approved project-media directories.

- [ ] **Step 4: Implement and register file lifecycle observers**

Delete only replaced or deleted paths owned by the affected record and contained within approved directories. Treat null and already-missing paths as successful no-ops.

- [ ] **Step 5: Run image tests**

Run: `php artisan test tests/Feature/ProjectImageServiceTest.php`

Expected: PASS.

- [ ] **Step 6: Commit media handling when Git is available**

Commit message: `feat: add optimized project image lifecycle`

### Task 7: Filament Project Management

**Files:**
- Create: `app/Policies/ProjectPolicy.php`
- Create: `app/Filament/Resources/Projects/ProjectResource.php`
- Create: `app/Filament/Resources/Projects/Schemas/ProjectForm.php`
- Create: `app/Filament/Resources/Projects/Tables/ProjectsTable.php`
- Create: `app/Filament/Resources/Projects/Pages/ListProjects.php`
- Create: `app/Filament/Resources/Projects/Pages/CreateProject.php`
- Create: `app/Filament/Resources/Projects/Pages/EditProject.php`
- Create: `app/Filament/Resources/Projects/RelationManagers/ImagesRelationManager.php`
- Create: `tests/Feature/Admin/ProjectResourceTest.php`

**Interfaces:**
- Consumes: administrator access from Task 1, project domain from Task 2, public preview route from Task 3, and image service from Task 6.
- Produces: authorized project CRUD, validated uploads, technology attachment, preview, publishing, filtering, searching, sorting, pagination, and reordering.

- [ ] **Step 1: Write failing project-resource tests**

Assert administrator creation and editing, non-admin denial, required and maximum lengths, unique/collision-safe slug behavior, HTTP(S)-only links, image MIME and size rules, relationship persistence, table search, publication/featured/status filters, ordering, preview URL, and deletion cleanup.

- [ ] **Step 2: Run project-resource tests and verify failure**

Run: `php artisan test tests/Feature/Admin/ProjectResourceTest.php`

Expected: FAIL because the resource does not exist.

- [ ] **Step 3: Implement policy, form schema, and upload integration**

Use Filament 5 schema APIs, the image service, reusable technology selection, explicit publication controls, and validation messages. Main image accepts JPEG, PNG, and WebP up to 5 MB; screenshots use the same limit.

- [ ] **Step 4: Implement table, actions, relation manager, and pages**

Include project preview, safe deletion confirmation, badges, filters, search, sortable columns, pagination, and drag-compatible display ordering when supported by the Filament table API.

- [ ] **Step 5: Run project-resource tests**

Run: `php artisan test tests/Feature/Admin/ProjectResourceTest.php`

Expected: PASS.

- [ ] **Step 6: Commit project administration when Git is available**

Commit message: `feat: add Filament project management`

### Task 8: Filament Content, Settings, and Message Management

**Files:**
- Create: `app/Policies/TechnologyPolicy.php`
- Create: `app/Policies/SkillPolicy.php`
- Create: `app/Policies/ExperiencePolicy.php`
- Create: `app/Policies/EducationPolicy.php`
- Create: `app/Policies/SiteSettingPolicy.php`
- Create: `app/Policies/ContactMessagePolicy.php`
- Create: `app/Filament/Resources/Technologies/TechnologyResource.php`
- Create: `app/Filament/Resources/Technologies/Schemas/TechnologyForm.php`
- Create: `app/Filament/Resources/Technologies/Tables/TechnologiesTable.php`
- Create: `app/Filament/Resources/Technologies/Pages/ListTechnologies.php`
- Create: `app/Filament/Resources/Technologies/Pages/CreateTechnology.php`
- Create: `app/Filament/Resources/Technologies/Pages/EditTechnology.php`
- Create: `app/Filament/Resources/Skills/SkillResource.php`
- Create: `app/Filament/Resources/Skills/Schemas/SkillForm.php`
- Create: `app/Filament/Resources/Skills/Tables/SkillsTable.php`
- Create: `app/Filament/Resources/Skills/Pages/ListSkills.php`
- Create: `app/Filament/Resources/Skills/Pages/CreateSkill.php`
- Create: `app/Filament/Resources/Skills/Pages/EditSkill.php`
- Create: `app/Filament/Resources/Experiences/ExperienceResource.php`
- Create: `app/Filament/Resources/Experiences/Schemas/ExperienceForm.php`
- Create: `app/Filament/Resources/Experiences/Tables/ExperiencesTable.php`
- Create: `app/Filament/Resources/Experiences/Pages/ListExperiences.php`
- Create: `app/Filament/Resources/Experiences/Pages/CreateExperience.php`
- Create: `app/Filament/Resources/Experiences/Pages/EditExperience.php`
- Create: `app/Filament/Resources/Education/EducationResource.php`
- Create: `app/Filament/Resources/Education/Schemas/EducationForm.php`
- Create: `app/Filament/Resources/Education/Tables/EducationTable.php`
- Create: `app/Filament/Resources/Education/Pages/ListEducation.php`
- Create: `app/Filament/Resources/Education/Pages/CreateEducation.php`
- Create: `app/Filament/Resources/Education/Pages/EditEducation.php`
- Create: `app/Filament/Resources/ContactMessages/ContactMessageResource.php`
- Create: `app/Filament/Resources/ContactMessages/Schemas/ContactMessageInfolist.php`
- Create: `app/Filament/Resources/ContactMessages/Tables/ContactMessagesTable.php`
- Create: `app/Filament/Resources/ContactMessages/Pages/ListContactMessages.php`
- Create: `app/Filament/Resources/ContactMessages/Pages/ViewContactMessage.php`
- Create: `app/Filament/Pages/EditSiteSettings.php`
- Create: `tests/Feature/Admin/ContentManagementTest.php`

**Interfaces:**
- Consumes: models from Task 2 and administrator access from Task 1.
- Produces: authorized Filament management for all non-project content, singleton settings editing, and read/unread contact-message actions.

- [ ] **Step 1: Write failing content-management tests**

Assert administrator CRUD, non-admin denial, uniqueness and validation rules, display ordering and publication controls, singleton settings behavior, social URL validation, contact messages being non-creatable/non-editable, and read/unread actions.

- [ ] **Step 2: Run focused tests and verify failure**

Run: `php artisan test tests/Feature/Admin/ContentManagementTest.php`

Expected: FAIL because resources and policies do not exist.

- [ ] **Step 3: Implement policies and focused Filament resources**

Create separate schema/table/page classes following Filament 5 conventions. Keep contact records viewable, filterable, markable, and deletable but not creatable or content-editable.

- [ ] **Step 4: Implement singleton site-settings page**

Expose owner identity, biography, email, social links, resume path, availability, and default SEO fields with exact validation and administrator authorization.

- [ ] **Step 5: Run content-management tests**

Run: `php artisan test tests/Feature/Admin/ContentManagementTest.php`

Expected: PASS.

- [ ] **Step 6: Commit remaining administration when Git is available**

Commit message: `feat: add portfolio content administration`

### Task 9: SEO Metadata and Sitemap

**Files:**
- Create: `app/Support/SeoData.php`
- Create: `app/Http/Controllers/SitemapController.php`
- Modify: `routes/web.php`
- Modify: `resources/views/components/layouts/app.blade.php`
- Modify: `resources/views/home.blade.php`
- Modify: `resources/views/projects/index.blade.php`
- Modify: `resources/views/projects/show.blade.php`
- Create: `resources/views/sitemap.blade.php`
- Create: `tests/Feature/SeoTest.php`
- Create: `tests/Feature/SitemapTest.php`

**Interfaces:**
- Consumes: site settings and published project scopes.
- Produces: `SeoData` value object/factory, page-specific title/description/canonical/Open Graph metadata, and named `sitemap` route.

- [ ] **Step 1: Write failing SEO and sitemap tests**

Assert metadata fallbacks, project SEO overrides, trusted absolute canonical URLs, optional valid project-image Open Graph metadata, XML content type, inclusion of public routes, and exclusion of unpublished projects.

- [ ] **Step 2: Run focused tests and verify failure**

Run: `php artisan test tests/Feature/SeoTest.php tests/Feature/SitemapTest.php`

Expected: FAIL because SEO data and sitemap are absent.

- [ ] **Step 3: Implement SEO data and render it through the shared layout**

Escape all content, use `APP_URL` as the trusted origin, omit image metadata when no public image exists, and do not generate a generic social card.

- [ ] **Step 4: Implement published-only XML sitemap**

Return the homepage, gallery, and each published project with canonical URLs and suitable last-modified dates.

- [ ] **Step 5: Run SEO and sitemap tests**

Run: `php artisan test tests/Feature/SeoTest.php tests/Feature/SitemapTest.php`

Expected: PASS.

- [ ] **Step 6: Commit SEO support when Git is available**

Commit message: `feat: add portfolio metadata and sitemap`

### Task 10: Documentation, Clean-Database Validation, and Final Quality Gate

**Files:**
- Modify: `README.md`
- Modify: `.env.example`
- Modify: `.gitignore`
- Modify: `tests/Feature/ExampleTest.php` or delete it if fully superseded
- Modify: `tests/Unit/ExampleTest.php` or delete it if fully superseded

**Interfaces:**
- Consumes: the complete application.
- Produces: reproducible Laragon setup instructions, placeholder replacement checklist, production notes, and recorded validation evidence.

- [ ] **Step 1: Replace starter documentation**

Document Laragon virtual-host access, MySQL database creation, environment setup, Composer and npm install, application key, migrations/seeding, storage link, admin creation, development/build commands, test commands, project publishing workflow, complete placeholder checklist, and production security/deployment notes.

- [ ] **Step 2: Verify environment examples and ignored files**

Confirm `.env.example` contains MySQL and administrator variable names but no secrets, `.env` remains ignored, uploaded files are not committed, and all documented commands match actual scripts.

- [ ] **Step 3: Run formatter and inspect the result**

Run: `vendor\bin\pint`

Expected: exits 0; review any changed files for formatting-only modifications.

- [ ] **Step 4: Run the complete test suite**

Run: `php artisan test`

Expected: all tests PASS with zero failures and zero errors.

- [ ] **Step 5: Build production frontend assets**

Run: `npm run build`

Expected: Vite exits 0 and emits versioned assets under `public/build`.

- [ ] **Step 6: Validate a clean MySQL migration and seed**

Create or use a dedicated empty validation database, run `php artisan migrate:fresh --seed --force`, inspect the seeded administrator behavior and DocuMind constraints, then restore the normal local database configuration if a dedicated connection was used.

- [ ] **Step 7: Create and verify the public storage link**

Run: `php artisan storage:link` and `php artisan about`.

Expected: the public storage link is present and the application reports MySQL and the expected environment.

- [ ] **Step 8: Smoke-test public and authenticated workflows**

Verify homepage, gallery, published detail, unpublished 404, contact submission and throttle, `/admin` guest redirect, administrator login, project create/edit/publish/reorder/preview/delete, content settings, and image replacement/deletion.

- [ ] **Step 9: Run route and configuration safety checks**

Run: `php artisan route:list` and `php artisan config:cache`; verify there is no registration route, all required named routes exist, then run `php artisan config:clear` for local development.

- [ ] **Step 10: Commit documentation and validation fixes when Git is available**

Commit message: `docs: add portfolio setup and deployment guide`
