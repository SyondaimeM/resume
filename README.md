# Sanish Maharjan's Portfolio

A Laravel portfolio with public experience, projects, education, skills, a resume
download, a contact form, and authenticated administration pages.

## Requirements

- PHP 8.5 and the extensions required by Composer.
- PHP GD for the self-hosted CAPTCHA image.
- Composer 2.
- A database supported by the configured Laravel connection.
- SQLite support for the isolated contact-form tests.

The application currently uses Laravel 9. Requiring PHP 8.5 does not by itself
certify all dependencies for that runtime; dependency maintenance remains
necessary. Check `composer audit` and the test suite before deployment.

## Production deployment

Back up the database and uploaded files before deployment. Keep server-specific
changes in a reviewed backup/stash rather than restoring old templates or scripts
over a newer release. Deploy committed code and assets from the same revision:

```sh
git pull --ff-only origin main
composer install --no-dev --prefer-dist --optimize-autoloader
php artisan migrate --force
php artisan view:clear
php artisan config:cache
php artisan queue:restart
```

Set `APP_ENV=production`, `APP_DEBUG=false`, and the correct HTTPS `APP_URL` in
the server's `.env` before caching configuration. Never regenerate an existing
application key. Restart the web PHP process through the hosting control panel
if it retains old code, and purge any full-page hosting cache.

Check all public pages, the CAPTCHA image, and the resume download after deploying.
Error emails report the effective `APP_ENV`; `local` in a live alert means the
server environment/configuration cache needs attention. Browser script errors
are reported through `ClientErrorController`, so its PHP source line in an alert
is the reporting location, not the original JavaScript failure location. Inspect
the matching Laravel log entry for the sanitized JavaScript filename and line.

## Local setup

```sh
git clone git@github.com:SyondaimeM/resume.git
cd resume
composer install
cp .env.example .env
php artisan key:generate
```

Set `APP_URL` and the database connection in `.env`, then run:

```sh
php artisan migrate
php artisan serve
```

Create the owner account before seeding a new database. Public registration is
disabled; provision the account through your trusted deployment process with a
secure password. The first account (lowest user ID) owns the public portfolio.
The seeder deliberately does not create an account with a default password.

```sh
php artisan db:seed --class=PortfolioSeeder
```

Open the address printed by Artisan. The configured profile portrait takes
precedence over the bundled portrait.

Administration routes under `/admin` require Laravel session authentication.
Public registration is disabled; this application does not require a JWT secret.

Never commit `.env`, credentials, or production database contents.

## Portfolio content

- Page templates: `resources/views/fields/`.
- Shared navigation, theme controls, footer, and contact form: `public/js/new/app.js`.
- Preserved seed content: `config/portfolio.php`.
- Idempotent importer: `database/seeders/PortfolioSeeder.php`.
- Public database adapter: `app/Services/PortfolioContent.php`.
- Shared inner-page layout: `resources/views/layouts/portfolio.blade.php`.
- Inner-page styling: `public/css/new/portfolio.css`.
- Styling: `public/css/new/`.
- Resume download: `public/Sanish_Maharjan_Resume_2026.pdf`.

Public pages read database records from the same models edited in administration.
Public controllers explicitly pass portfolio data to their views; rendering does
not depend on view-composer event registration.
Public JavaScript and CSS URLs include a content hash so browsers fetch changed
files after deployment instead of retaining the previous navigation/contact form.
Deploy the application and public assets together, then clear compiled views with
`php artisan view:clear`. Purge any full-page hosting cache after deployment.
Legacy HTML profile summaries are rendered as escaped plain text without changing
the stored record. Review imported profile, education, and experience records in
admin: seeding deliberately preserves existing edits and does not overwrite them.
Cards are server-rendered Blade views; no duplicate JavaScript content arrays
or config fallbacks resurrect hidden/deleted records. The database must be
migrated and seeded before publishing.

- `/admin/experience`: roles, companies, locations, dates, bullets, logos/wordmarks.
- `/admin/education`: qualifications, dates, courses, activities, and achievements.
- `/admin/skill`: skills, group names/descriptions, and display/group ordering.
- `/admin/project`: project descriptions, tools, images, links, and featured status.
- `/admin/portfolio`: public name, summary, roles, social URLs, and PDF upload.
- `/admin/profile`: existing profile and portrait editor.

Content editors are restricted to the first account. New/edit/hide/delete actions
update public pages immediately. Lower order values appear first. Images reference
existing paths below `public/images/`, or upload JPEG/PNG/WebP files up to 5 MB.
Uploads receive generated names below `public/images/uploads/` (ignored by Git);
include this directory in deployment backups. Replacing an image retains the
old file, and failed database writes clean up the newly uploaded file. Image
paths and external URLs are validated.
Use plain text with one bullet/course/tool per line. Legacy rich text is converted
to safe plain text for public display.

Skill descriptions appear on the public cards. Group description/order edits
apply to every skill with the same group name. Changing a skill's group name
moves that skill, not the whole group. Date/status edits clear an unchanged
custom display-period label so old years do not override newly entered dates;
enter a different label in the same save to explicitly override the computed dates.

The seeder retains the original code content, including all three education
entries, dates, activities, additional projects, and C/C++ skills, alongside
resume updates. Year-only dates use year boundaries internally and retain their
original year-only display labels. It never deletes records or overwrites
already-imported edits/visibility. Existing matching entries are retained.
Re-seeding can restore a seed entry that was deleted; hide it instead to preserve
an intentional publication decision.

The supplied 2026 PDF is a separate, unchanged document, not an auto-generated
export. Update the records in admin, then upload a revised PDF in
`/admin/portfolio`. The shared download button uses the uploaded path on every
page. Uploads get generated filenames under `public/resumes/` (ignored by Git).
Old PDFs are retained rather than deleted; include uploads in deployment backups.
On deployments with cached configuration, rebuild it after changing environment
settings with `php artisan config:cache`.

Skills are ordered by relevance to the PHP/backend resume, not a measured market
popularity ranking or proficiency score. Existing education details absent from
the PDF are preserved, not discarded. An optional display period stays in normal
flow, so dates never cover qualification headings.

Projects from the resume are separated from additional work preserved from the
existing portfolio. Company images retain their aspect ratio; projects without
screenshots use text banners. The shared inner-page layout uses responsive grids
instead of fixed-height illustration sections and absolutely positioned timelines.
The native mouse pointer remains visible; the decorative cursor is disabled on
small screens, touch input, and reduced-motion preferences.

The PayEdge project links to RBC's public product page for product context.
Personal contributions and NDDS details are based on the supplied resume.
The bundled portrait at `public/images/Portrait/pp.jpg` is the same asset served
by the live site. It is used when the profile's `photo_path` is empty; a configured
profile photo still takes precedence.

## Contact form

`POST /message` accepts `name` (up to 255 characters), `email` (a valid address
up to 255 characters), `message` (up to 10,000 characters), and `captcha`
(the five-character verification code).

The form stores submissions and a database-queued email notification in one
transaction. Requests use CSRF protection. JSON responses use HTTP 201 on
successful storage/queueing, 422 for invalid input, and 503 if storage or queue
persistence fails. SMTP delivery is asynchronous and is not claimed by HTTP 201.
Database exceptions are
reported through Laravel's exception handler rather than exposed to visitors.
The browser preserves the message on failure and confirms success only after
the server acknowledges storage.

`GET /contact/captcha` generates a PNG using GD and stores only a hash of its
answer in the current session. It expires after five minutes and is consumed
on verification, whether the answer is right or wrong. Requesting a new image
replaces the previous code, including in another tab in the same session.
The browser refreshes the code after a submission attempt. Images are not cached.
No third-party keys, scripts, or verification services are used.

Contact requests are limited to five attempts per minute per IP, and challenge
images to twenty requests per minute per IP. HTTP 429 signals a rate limit.
Configure Laravel's trusted proxies correctly when deploying behind a proxy,
and use a shared cache when deploying more than one application instance.

This is a visual CAPTCHA with a refresh control, not an audio challenge or
a guarantee against sophisticated OCR-based bots. It is not an accessible
equivalent to a managed challenge provider; a non-visual alternative remains
necessary for visitors who cannot read image challenges.

## Email notifications and errors

Set these in `.env` (never in committed code):

```dotenv
PORTFOLIO_NOTIFICATION_EMAIL=your-notification-address@example.com
PORTFOLIO_ERROR_EMAILS=true
PORTFOLIO_MAIL_QUEUE_CONNECTION=database
```

Configure real `MAIL_*` SMTP settings, including the authenticated sender.
Laravel 9 supports `MAIL_ENCRYPTION=tls` or `ssl`; `MAIL_SCHEME=smtps` is also
mapped to SSL. Mail connections time out after ten seconds.

Run a supervised worker in production and restart it after deployments:

```sh
php artisan queue:work database --sleep=3 --tries=3 --timeout=30
php artisan queue:failed
php artisan queue:retry <failed-job-uuid>
php artisan queue:restart
```

Each new contact queues an email containing the submitted name/email/message,
with the visitor as Reply-To. Failures retry three times with backoff and then
remain in `failed_jobs`; the contact remains stored. Email delivery is at-least-once:
an ambiguous SMTP acknowledgment can cause a duplicate on retry.

Reported PHP exceptions, error-level application logs, and captured browser
runtime/unhandled-promise errors trigger system alerts. Normal validation errors,
404s, and authorization failures are not system-error alerts. Browser reporting
uses CSRF protection and request throttling; it excludes messages, stack traces,
URLs/query strings, and form data. Cross-origin script errors are not captured.
System emails contain only a reference, exception class, source basename/line,
environment, and timestamp; inspect server logs for details.

Error emails use synchronous SMTP so a database outage can still trigger an alert.
Identical failures are limited to one alert per five minutes using a local file
cache. Multiple servers each have their own throttle. Mail/reporting failures
are logged without recursion and never replace the original exception. Alerts
cannot operate before Laravel boots or during a total server/SMTP outage; external
uptime monitoring is still needed. No browser telemetry is sent to third parties.

## Validation

```sh
composer validate
composer check-platform-reqs
vendor/bin/phpunit --filter 'ContactMessageTest|PortfolioPageTest|PortfolioAdminTest|EmailNotificationTest' tests/Feature
node --check public/js/new/app.js
node --check public/js/new/cursor.js
node --check public/js/new/error-reporting.js
php -l config/portfolio.php
git diff --check
```

Node.js is needed only for the JavaScript syntax checks above, not for an asset
build. Contact tests use an in-memory SQLite database and do not modify the
configured development database. Mail uses fakes/array transport, and system alerts
are disabled by default in the test environment.

The full suite can be run with `vendor/bin/phpunit`. Existing legacy tests
include a database-dependent education test that does not boot Laravel; it
needs a separate test-fixture/bootstrap update before the full suite is reliable.
