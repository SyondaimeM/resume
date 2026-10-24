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

Open the address printed by Artisan. The homepage expects a user record.
On a new, disposable development database, `php artisan db:seed` creates a sample
user and skills. Replace sample profile information before publishing. The
existing seeder does not persist education or experience records.

Administration routes under `/admin` require Laravel session authentication.
Public registration is disabled; this application does not require a JWT secret.

Never commit `.env`, credentials, or production database contents.

## Portfolio content

- Page templates: `resources/views/fields/`.
- Shared navigation, theme controls, footer, and contact form: `public/js/new/app.js`.
- Experience cards: `public/js/new/experience.js`.
- Project cards: `public/js/new/project.js`.
- Styling: `public/css/new/`.
- Resume download: `public/Sanish_Maharjan_Resume_2026.pdf`.

The public experience and project cards currently use static JavaScript data,
not the corresponding admin records. Keep this distinction in mind when
editing content. Company images retain their aspect ratio; projects without
screenshots use text banners.

The PayEdge project links to RBC's public product page for product context.
Personal contributions and NDDS details are based on the supplied resume.
The bundled portrait at `public/images/Portrait/pp.jpg` is the same asset served
by the live site. It is used when the profile's `photo_path` is empty; a configured
profile photo still takes precedence.

## Contact form

`POST /message` accepts `name` (up to 255 characters), `email` (a valid address
up to 255 characters), `message` (up to 10,000 characters), and `captcha`
(the five-character verification code).

The form stores submissions in the `messages` table; it does not send email.
Requests use CSRF protection. JSON responses use HTTP 201 on successful storage,
422 for invalid input, and 503 for a database failure. Database exceptions are
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

## Validation

```sh
composer validate
composer check-platform-reqs
vendor/bin/phpunit --filter 'ContactMessageTest|PortfolioPageTest' tests/Feature
node --check public/js/new/app.js
node --check public/js/new/experience.js
node --check public/js/new/project.js
node --check public/js/new/education.js
git diff --check
```

Node.js is needed only for the JavaScript syntax checks above, not for an asset
build. Contact tests use an in-memory SQLite database and do not modify the
configured development database.

The full suite can be run with `vendor/bin/phpunit`. Existing legacy tests
include a database-dependent education test that does not boot Laravel; it
needs a separate test-fixture/bootstrap update before the full suite is reliable.
