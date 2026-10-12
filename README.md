# Developer Portfolio

A responsive Laravel portfolio with a secure Filament administration dashboard. Public visitors only see published projects; administrators manage projects, images, technologies, skills, experience, education, site settings, and contact messages without editing source code.

## Stack and requirements

- PHP 8.2 or newer with `pdo_mysql`, `gd`, and `zip`
- Laravel 12
- MySQL or MariaDB
- Node.js and npm
- Filament 5
- Laragon on Windows for the documented local setup

The project uses Blade and Tailwind CSS 4 for the public interface. Project images are normalized to generated WebP files through Intervention Image and Laravel's public filesystem disk.

## Laragon setup

1. Put the project at `C:\laragon\www\portfolio` and start Laragon's Apache/Nginx and MySQL services.
2. Confirm the PHP `gd`, `pdo_mysql`, and `zip` extensions are enabled from **Laragon → PHP → Extensions**. Restart Laragon after changing extensions.
3. Create two UTF-8 databases using HeidiSQL, phpMyAdmin, or the Laragon terminal:

   ```sql
   CREATE DATABASE portfolio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   CREATE DATABASE portfolio_testing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

4. Copy `.env.example` to `.env`, then adjust `DB_USERNAME`, `DB_PASSWORD`, and `APP_URL` if your Laragon configuration differs. The default Laragon URL is `http://portfolio.test`.
5. Choose local administrator values in `.env`. Use a unique development password; never add `.env` to source control:

   ```dotenv
   ADMIN_NAME="Your Name"
   ADMIN_EMAIL=you@example.test
   ADMIN_PASSWORD="choose-a-strong-local-password"
   ```

6. Install and initialize the application:

   ```powershell
   composer install
   npm install
   php artisan key:generate
   php artisan migrate --seed
   php artisan storage:link
   npm run build
   ```

7. Open `http://portfolio.test` for the portfolio and `http://portfolio.test/admin` for the dashboard.

If Laragon does not create the virtual host automatically, use **Menu → www → portfolio** or reload Laragon. As an alternative, run `php artisan serve` and open the URL it prints.

## Local development

Run the Laravel server, queue listener, log viewer, and Vite development server together:

```powershell
composer run dev
```

For a production asset build:

```powershell
npm run build
```

Uploaded media is stored under `storage/app/public`. The `public/storage` link must exist anywhere the site serves uploaded images.

## Administrator setup

`DatabaseSeeder` calls `AdminUserSeeder`. The administrator is only created or updated when `ADMIN_NAME`, `ADMIN_EMAIL`, and `ADMIN_PASSWORD` are all non-empty.

After changing those environment values, create or update the local administrator without reseeding portfolio content:

```powershell
php artisan db:seed --class=AdminUserSeeder
```

Public registration is not installed. A user must have `is_admin = 1` to access Filament, and policies authorize content actions server-side.

## Adding and publishing a project

1. Sign in at `/admin` and open **Projects**.
2. Choose **New project** and add a factual title, unique readable slug, short summary, full description, status, and display order.
3. Select existing technologies or create reusable technologies from **Technologies**.
4. Upload an optional JPEG, PNG, or WebP main image up to 5 MB. The application generates a safe filename and optimized WebP output.
5. Save the project as unpublished while editing. Draft URLs return 404 publicly.
6. Add ordered screenshots from the project's edit page.
7. Use **Featured** to include a published project in the homepage selection, then enable **Published** when its content is ready.
8. Drag records in reorder mode or edit **Display order** to control presentation. Lower numbers appear first.

Project tables support searching, sorting, pagination, status filtering, published/featured filtering, editing, previewing published pages, reordering, and deletion.

## Tests and quality checks

Tests use the dedicated `portfolio_testing` MySQL database configured in `phpunit.xml`. Change those non-secret local values if your MySQL account differs.

```powershell
vendor\bin\pint
php artisan test
npm run build
```

To verify the schema and development records from a clean database:

```powershell
php artisan migrate:fresh --seed --force
```

Only run `migrate:fresh` against a disposable local or test database because it deletes all existing tables.

## Seeded portfolio information

`PortfolioContentSeeder` uses the factual information supplied in Kent C. Dayola's résumé for the owner name, professional title, biography, email address, GitHub profile, availability, skills, experience, education, and SeawiseHub draft. It deliberately does not publish a phone number or home location. Seeding is idempotent and removes only the original visibly labeled placeholder skill, experience, and education records.

The seeded project visibility is deliberate:

- **DocuMind** is published, featured, marked **In development**, and includes factual content verified from the supplied live deployment. No repository URL or project image has been supplied.
- **HelpDesk Ticketing System** is published, featured, marked **Completed**, and links to the supplied live deployment. Its description is limited to features verified from the public registration and sign-in pages.
- **SeawiseHub** remains unpublished and marked **In development** with résumé-based descriptions and technologies. No repository URL, live-demo URL, completion date, or images have been supplied.

## Personal information still to provide or review

- LinkedIn profile URL
- Résumé file for the public download link (the source résumé is not copied automatically)
- DocuMind screenshots or main image and repository URL
- HelpDesk Ticketing System screenshots or main image, repository URL, and confirmed completion date
- SeawiseHub live-demo URL, repository URL, confirmed project status/completion date, screenshots, and final SEO text
- Main and gallery images for both projects
- Whether either reviewed project should be featured and published
- Optional skill proficiency labels and icons
- Exact education start/end dates, if you want dates displayed instead of the résumé's graduation-year note
- Any approved public location information

The résumé also lists freeCodeCamp Responsive Web Design, freeCodeCamp JavaScript Algorithms and Data Structures, and Cisco Networking Academy CCNA – Introduction to Networks certifications. The current focused MVP has no certification content type, so these are not displayed rather than being forced into an unrelated section.

## Security and deployment

- Set `APP_ENV=production`, `APP_DEBUG=false`, and a correct HTTPS `APP_URL` in production.
- Generate a unique production `APP_KEY`; never copy local credentials or commit `.env`.
- Use a dedicated least-privilege database account and a strong administrator password.
- Terminate TLS at the web server or trusted proxy, configure trusted proxies/hosts as appropriate, and redirect HTTP to HTTPS.
- Point the web root at `public`, never at the project root.
- Run `composer install --no-dev --optimize-autoloader`, `npm ci && npm run build`, `php artisan migrate --force`, `php artisan storage:link`, and `php artisan optimize` during deployment.
- Ensure `storage` and `bootstrap/cache` are writable by the web process, while source and environment files are not publicly writable.
- Back up the database and `storage/app/public`; test restores before relying on them.
- Preserve the contact rate limiter and consider upstream rate limiting or a CAPTCHA if abuse appears.
- Review uploaded-image limits, PHP upload limits, mail/log retention, session cookies, and database backups for the target environment.
- Run a queue worker if the application later adds queued mail or background processing.
- Keep Laravel, Filament, PHP, Composer, npm dependencies, and the server patched. Run `composer audit` and `npm audit` as part of release maintenance.

## Public behavior

- Only published projects appear on the homepage, gallery, sitemap, or detail routes.
- Featured projects must also be published.
- Missing, draft, and newly unpublished slugs return 404.
- External links are restricted to HTTP(S) and open with safe new-tab attributes.
- Contact submissions are validated, stored for admin review, protected by a honeypot, and limited to three attempts per minute per normalized email/IP identity.
- Public pages include canonical URLs, Open Graph metadata, accessible landmarks, focus states, reduced-motion support, and system-aware light/dark themes.
