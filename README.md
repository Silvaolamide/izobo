# IZOBO Campaign Website

A lightweight campaign landing page plus a Laravel/MySQL registration and administration backend.

## What has been added

- Redesigned `/join` registration experience.
- State → LGA → Ward cascading selectors.
- Edo State is selected by default.
- Owan East is selected by default.
- Owan East wards are seeded from INEC's published Edo CVR location data.
- MySQL-backed registrations.
- Secure admin login and aggregate dashboard.
- SEO-friendly registration page with accessible form labels and validation.
- No campaign supporter profiling or political persuasion targeting features.

## Laravel application

The Laravel application lives in `laravel/` so the existing landing-page work can remain lightweight while the backend is developed separately.

### Local setup

```bash
cd laravel
composer install
copy .env.example .env
php artisan key:generate
```

Create a MySQL database, update `.env`, then run:

```bash
php artisan migrate --seed
php artisan serve
```

The registration page is available at `/join` and the admin dashboard at `/admin/login`.

### Admin credentials

Set these before seeding:

```env
IZOBO_ADMIN_EMAIL=your-admin-email@example.com
IZOBO_ADMIN_PASSWORD=use-a-long-random-password
```

Never commit `.env` or production credentials.

## cPanel deployment

Laravel should be served from its `public/` directory, not the project root. This protects `.env`, `app/`, `config/`, and other private files. On cPanel, point the domain/subdomain document root to `laravel/public` or place the Laravel project outside `public_html` and point the domain's document root to its `public` directory.

Typical deployment steps:

1. Create a MySQL database and database user in cPanel.
2. Upload/clone the repository.
3. Set the domain document root to `laravel/public`.
4. Run `composer install --no-dev --optimize-autoloader` in `laravel/` using Terminal/SSH, or upload the generated `vendor/` directory if SSH/Composer is unavailable.
5. Create `.env` from `.env.example` and enter the cPanel MySQL credentials.
6. Run `php artisan key:generate`.
7. Run `php artisan migrate --seed`.
8. Ensure `storage/` and `bootstrap/cache/` are writable by PHP.
9. Set `APP_DEBUG=false` in production.
10. Use HTTPS and set `APP_URL` to the real site URL.

## Location data

The current campaign target is Owan East, Edo State. The seeded Owan East wards are:

- EMAI 1
- EMAI II
- IHIEVEBE I
- IHIEVBE II
- UOKHA/AKE
- IGUE/IKAO
- IVBIANION
- OTUO I
- OTUO II
- IVBIADAOBI
- WARRAKE

The location tables are intentionally relational so the application can be expanded with additional Nigerian LGA/ward data later without changing the registration form.

## Admin dashboard

The dashboard provides aggregate operational statistics such as:

- total registrations
- registrations today
- registrations in the last 7/30 days
- registrations by LGA
- registrations by Ward
- participation-interest breakdown
- recent registration trend

The dashboard deliberately does not expose individual contact details in the statistics view.

## SEO and usability priorities

The registration page uses semantic headings, descriptive metadata, accessible labels, mobile-first controls, server-side validation, and cascading location selectors. The form also records explicit consent for campaign coordination use of submitted information.

## Source note

The Owan East ward names/codes in the seed data follow INEC's published 2024 Edo/OnDo CVR registration-centre document. Verify the location dataset again before a production election campaign because official administrative data can change.
