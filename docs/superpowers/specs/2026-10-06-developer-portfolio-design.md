# Developer Portfolio and Admin Dashboard Design

## Purpose

Build a professional developer portfolio in the existing Laravel application. The public website presents the owner's work, while an authenticated administration panel manages portfolio content without source-code edits.

The portfolio is independent of the DocuMind Next.js application. DocuMind appears only as a clearly labeled placeholder project with an `In development` status and no invented repository or demo links.

## Existing Application

The repository is a fresh Laravel 12.69.3 application running PHP 8.2.0. It currently uses Blade, Tailwind CSS 4, Vite, PHPUnit 11, Laravel Pint, and SQLite. Filament is not installed. The implementation will use MySQL and Filament 5, whose current requirements support Laravel 12, PHP 8.2, and Tailwind CSS 4.1 or newer.

## Scope

### Public portfolio

The public site contains:

- Responsive navigation
- Hero and about sections
- Skills and technologies
- Featured projects
- Paginated project gallery
- Slug-based project detail pages
- Experience and education
- Contact form and contact links
- Footer with GitHub, LinkedIn, email, and resume links
- Light and dark themes
- SEO metadata and an XML sitemap

### Administration

The Filament panel is available under `/admin` and provides authenticated content management for:

- Projects and ordered project screenshots
- Technologies
- Skills
- Experience
- Education
- Global site settings
- Contact messages

Project management includes creation, editing, previewing, publishing, featuring, ordering, searching, filtering, sorting, and deletion.

### Non-goals

- Public account registration
- Invented biography, employment, education, testimonials, achievements, or project facts
- Email delivery or newsletter functionality for the initial contact form
- Localization or multi-language content
- Analytics, comments, or client portals
- Integration with the separate DocuMind codebase

## Architecture

The application has three layers:

1. Public Blade controllers and views render the portfolio.
2. Filament resources provide the secured administration experience.
3. Shared Eloquent models, policies, query scopes, services, and observers enforce content and file-management rules.

Public and administration interfaces share the same MySQL data but use separate routes and presentation components. Public project queries always apply publication rules and deterministic ordering.

## Data Model

### Users

The existing users table gains an indexed `is_admin` boolean. Only authenticated users with this flag may access the Filament panel or perform administration actions.

### Projects

Projects contain:

- Title and unique slug
- Short and full descriptions
- Main image path
- Repository and live-demo URLs
- Status
- Featured and published flags
- Display order
- Completion date
- SEO title and description
- Created and updated timestamps

Indexes support public publication and ordering queries, featured queries, status filtering, and slug lookup.

### Project images

Each project has zero or more additional screenshot records containing an image path, alternative text, and display order. Deleting a project cascades its image records and removes the associated files through application-level cleanup.

### Technologies and project relationships

Technologies are reusable records with a unique name and slug. Projects and technologies have a many-to-many relationship through an indexed pivot table. Technology names are not stored as comma-separated project text.

### Skills

Skills contain a name, optional category, optional proficiency label, optional icon identifier, publication state, and display order.

### Experience and education

Experience and education records store only owner-supplied or explicitly labeled placeholder details. Each record supports date ranges, current-state handling, descriptions, publication state, and display order.

### Site settings

A singleton site-settings record stores editable global information: name, professional title, biography, email, social links, resume path, availability, default SEO title, and default SEO description. The application handles a missing settings record with safe placeholder defaults.

### Contact messages

Contact messages store name, email, subject, message, submission metadata needed for abuse review, read state, and timestamps. The administration panel can view, filter, mark, and delete messages. The MVP does not send email notifications.

## Public Behavior and Data Flow

The homepage controller loads site settings and published, ordered content with eager-loaded relationships. Featured projects require both `published` and `featured` flags. The project gallery uses pagination and only exposes published projects. Project detail routing uses the slug and returns a 404 response for missing or unpublished records.

The contact form validates input, uses CSRF protection, and applies a dedicated rate limiter. Successful submissions create a contact-message record and return an accessible success message. Validation failures preserve safe input and return field-specific messages.

External repository, demo, and social links accept only normalized HTTP or HTTPS URLs and render with safe new-tab attributes.

## Administration and Authorization

Filament 5 supplies the panel authentication flow and content resources. Public registration remains unavailable. The `User` model implements Filament panel-access checks, and Laravel policies authorize resource actions for administrator users.

The project resource includes:

- Validated project fields and relationship selectors
- Automatic slug generation with uniqueness enforcement
- Main-image and multi-screenshot uploads
- Publication, featured, status, and ordering controls
- Preview action for the public-facing result
- Search across title, slug, and descriptions
- Filters for publication, featured state, and status
- Sortable dates and display order
- Paginated tables and deletion confirmation

Other content types use focused resources with ordering and publication controls where applicable. Contact messages are read-only except for state changes and deletion.

## Upload Lifecycle

Uploaded project images use Laravel's public filesystem disk and generated storage names rather than client filenames. Validation restricts uploads to approved image MIME types and a documented maximum size. Image dimensions are reserved in the interface, and display markup uses responsive loading behavior.

Replacing a main image removes the obsolete file only after the new record state is safely persisted. Deleting screenshots or projects removes their files. Cleanup must avoid deleting shared or default assets and must remain testable with Laravel's fake storage.

The project documents `php artisan storage:link` for local and production setup.

## Visual Design

The public portfolio uses an editorial, developer-focused direction with strong typography, warm neutral surfaces, a restrained accent color, generous spacing, crisp borders, and subtle interaction feedback. It avoids glassmorphism, excessive gradients, glowing effects, and distracting animation.

Reusable Blade components implement the site shell, navigation, theme control, section headings, project cards, technology badges, metadata, empty states, form controls, and footer. The layouts adapt across mobile, tablet, and desktop sizes.

Light and dark color systems provide sufficient contrast. Semantic landmarks, logical heading order, visible focus indicators, keyboard-operable controls, descriptive alternative text, and reduced-motion styles are mandatory.

## Placeholder Content

Factories and seeders create clearly labeled development placeholders. They do not imply real biography or achievements. The placeholder set includes:

- Owner name
- Professional title
- Biography
- Contact email
- GitHub and LinkedIn URLs
- Resume path
- Availability text
- Experience records
- Education records
- Skills and technology selections
- Project descriptions and media

DocuMind is seeded only as an `In development` placeholder without a repository URL or live-demo URL. It remains unpublished until the owner supplies accurate content.

## Security

- All state-changing forms use CSRF protection.
- Every administration action requires authentication and policy authorization.
- Public registration is not exposed.
- Development administrator credentials come from environment values or an interactive command and are never committed.
- Passwords use Laravel's configured hashing.
- Contact submissions are validated and rate-limited.
- External URLs are validated and normalized.
- Upload type and size are enforced server-side.
- Production documentation requires debug mode to be disabled and secrets to remain outside version control.
- Error pages do not expose stack traces or configuration.

## SEO and Performance

Each public page supplies a title, description, canonical URL, and Open Graph text metadata. Project pages use their SEO override fields when present and fall back to project content. No social-preview image is generated; a project image may be referenced when a valid public image exists.

An XML sitemap lists the homepage, project gallery, and published project pages. Database access uses eager loading and focused selects where useful. Galleries and administration tables paginate. Public JavaScript remains limited to theme and navigation interactions.

## Error and Empty States

- Missing or unpublished project slugs return the standard 404 page.
- Empty project, skill, experience, and education collections render purposeful messages or omit optional sections cleanly.
- Missing settings use explicit placeholder values without throwing errors.
- Upload and form failures show field-level validation feedback.
- Administration actions use Filament success and failure notifications.

## Testing Strategy

Factories support focused tests without relying on seed data. Automated feature tests cover:

- Homepage, gallery, and project-detail availability
- Draft projects being absent from public pages
- Published and featured project visibility and ordering
- Slug resolution and 404 behavior
- Admin authentication requirements
- Non-administrator panel denial and action authorization
- Project creation, relationship persistence, and validation
- Image type and size validation with fake storage
- Contact-form validation, persistence, success feedback, and rate limiting
- Sitemap exclusion of unpublished projects

## Documentation and Operations

The starter README is replaced with setup and operating instructions covering:

- Laragon prerequisites and virtual-host use
- MySQL database creation and `.env` configuration
- Dependency installation
- Application key generation
- Migrations and seeders
- Storage linking
- Frontend asset development and production builds
- Environment-driven or interactive administrator creation
- Tests and formatting
- Adding, previewing, ordering, and publishing a project
- Every placeholder that still needs replacement
- Production deployment, permissions, caching, queue, debug, secret, backup, and HTTPS guidance

The `.env.example` uses MySQL defaults suitable for documentation and contains blank administrator credential variables. It contains no real credentials.

## Completion Criteria

The implementation is complete when:

1. Fresh MySQL migrations and seeders succeed.
2. The public pages and contact flow work as designed.
3. An administrator can authenticate and manage all specified content.
4. Non-administrator access is denied.
5. Upload replacement and deletion behavior is safe.
6. Laravel Pint completes successfully.
7. The complete PHPUnit suite passes.
8. The production frontend asset build succeeds.
9. Routes, the storage link, sitemap, public visibility rules, and administration workflow pass smoke checks.
10. The README and `.env.example` fully document Laragon setup and remaining placeholders.
