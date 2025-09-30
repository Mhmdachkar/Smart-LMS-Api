<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="320" alt="Laravel Logo">
</p>

# Smart LMS API

A production-grade Learning Management System API built with Laravel 10, JWT authentication, and a Filament-based admin panel. It provides user auth, role-based access, course/enrollment foundations, and a customizable dashboard with live statistics.

## Table of Contents

- Overview
- Tech Stack
- Project Structure
- Getting Started
- Environment & Configuration
- Database & Migrations
- Authentication (JWT)
- API Endpoints (v1)
- Postman Collection
- Admin Panel (Filament)
- Custom Dashboard Widget
- Common Commands
- Troubleshooting
- What We Implemented

## Overview

Smart LMS API powers modern educational platforms with secure authentication, clean REST APIs, and a convenient admin UI. It’s designed for scalability and clarity, following Laravel best practices with Eloquent relationships, form request validation, and consistent API responses.

## Tech Stack

- Laravel 10 (PHP 8.2)
- MySQL
- JWT Auth: `tymon/jwt-auth`
- Admin Panel: Filament v3
- Caching/Queues: Laravel defaults (extendable)

## Project Structure

- `routes/api.php` → API v1 routing and grouping
- `app/Http/Controllers/Api/V1` → Versioned API controllers
- `app/Http/Requests/Api/V1` → Form Requests (validation)
- `app/Models` → Eloquent models (Users, Courses, Enrollments, ...)
- `app/Http/Middleware/JwtAuthMiddleware.php` → JWT guard + error handling
- `app/Filament/Widgets/LMSStatsWidget.php` → Custom dashboard metrics
- `postman/Smart-LMS-API.postman_collection.json` → API tests/examples

## Getting Started

1) Clone and install

```bash
git clone <repo-url>
cd Smart-LMS-Api
composer install
cp .env.example .env
php artisan key:generate
```

2) Configure database in `.env`

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smart_lms_api
DB_USERNAME=root
DB_PASSWORD=secret
```

3) JWT setup

```bash
php artisan jwt:secret
```

4) Migrate

```bash
php artisan migrate
```

5) Serve (example)

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

## Environment & Configuration

- `config/auth.php` includes an `api` guard using `jwt`.
- Email validation is environment-aware: `email:rfc` locally, `email:rfc,dns` in production.
- Middleware alias `jwt.auth` applies JWT verification and user status checks (`active`, `suspended`, `inactive`).

## Database & Migrations

Key changes implemented:

- Users table aligned with LMS needs: `first_name`, `last_name`, `phone`, `avatar_url`, `role` (student|instructor|admin), `status` (active|suspended|inactive), `last_login_at`.
- Backfill from legacy `name` → split into first/last names; drop legacy column.
- `user_profiles` supports `preferences` and `social_links` (JSON) with proper `$fillable` and `$casts`.

Models updated with relationships and safe mass assignment (`$fillable`). Notable:

- `User` implements `JWTSubject` and `FilamentUser` (restricts panel to admins), plus `getNameAttribute()`.
- `Course`, `Category`, `Section`, `Enrollment` relationships established.

## Authentication (JWT)

Public endpoints:

- POST `/api/v1/auth/register`
- POST `/api/v1/auth/login`

Protected (uses `jwt.auth`):

- GET `/api/v1/auth/me`
- POST `/api/v1/auth/logout`
- POST `/api/v1/auth/refresh`

Responses are standardized via `BaseApiController`.

## API Endpoints (v1)

Auth group sample (see `routes/api/v1/auth.php`):

```text
POST   /api/v1/auth/register
POST   /api/v1/auth/login
GET    /api/v1/auth/me           (jwt)
POST   /api/v1/auth/logout       (jwt)
POST   /api/v1/auth/refresh      (jwt)
```

Users/Courses are scaffolded for admin via Filament; public CRUD APIs will be extended in subsequent phases.

## Postman Collection

Use the included collection:

- Import `postman/Smart-LMS-API.postman_collection.json`
- Collection includes Register/Login/Me/Logout/Refresh with pre-request helpers.

## Admin Panel (Filament)

- Admin-only access via `User::canAccessPanel()` checking `role === 'admin'`.
- Navigate to `/admin` and login as an admin user.
- Known requirement: PHP `intl` extension for certain numeric formatting. If missing, dashboard still works; some list views may fail with `Class "NumberFormatter" not found`.

Enable `intl` on Windows (php.ini):

```ini
; Uncomment or add
extension=intl
```

Restart PHP/serve after enabling.

## Custom Dashboard Widget

`app/Filament/Widgets/LMSStatsWidget.php` shows live metrics:

- Total Users, Students, Instructors
- Total Courses, Categories
- Total/Active Enrollments, Completed Courses

These values update as you add data via API or tinker.

## Common Commands

Add users via tinker (PowerShell-safe):

```powershell
php artisan tinker --execute="App\Models\User::create(['first_name'=>'John','last_name'=>'Doe','email'=>'john.doe@example.com','password'=>bcrypt('Password123!'),'role'=>'student','status'=>'active']); echo 'User created';"
```

Counts:

```powershell
php artisan tinker --execute="echo 'Users: ' . App\Models\User::count();"
php artisan tinker --execute="echo 'Students: ' . App\Models\User::where('role','student')->count();"
```

Enrollment lifecycle:

```powershell
php artisan tinker --execute='$s=App\Models\User::where("role","student")->first(); $c=App\Models\Course::first(); App\Models\Enrollment::create(["course_id"=>$c->id,"student_id"=>$s->id,"status"=>"active","amount_paid"=>99.99]); echo "Enrollment created";'
php artisan tinker --execute='$e=App\Models\Enrollment::latest()->first(); $e->update(["status"=>"completed"]); echo "Enrollment updated";'
php artisan tinker --execute='App\Models\Enrollment::latest()->first()?->delete(); echo "Enrollment deleted";'
```

## Troubleshooting

- Could not open input file: artisan → Ensure you are in the project root: `cd Smart-LMS-Api`.
- PowerShell quoting errors → Use single-quoted `--execute` and double quotes inside PHP.
- 422 on register → Check validation errors (password strength, unique email). Use try/catch in PowerShell to print JSON.
- `Class "NumberFormatter" not found` → Enable PHP `intl` extension or avoid `->money()` formatting in tables.
- JWT unauthorized → Ensure `config/auth.php` has `api` guard driver `jwt`, run `php artisan jwt:secret`, and clear config cache if needed.

## What We Implemented

- Registration API with robust validation and profile auto-creation.
- JWT Authentication (login/logout/refresh/me) with `jwt.auth` middleware.
- Environment-aware email validation (`rfc` vs `rfc,dns`).
- Database migrations aligning users schema with LMS needs.
- Fixed mass-assignment for `UserProfile` and added JSON casts.
- Filament admin panel accessible only to admins.
- Custom LMS statistics dashboard widget.
- Postman collection for Tasks 6–8.

## Roadmap

- Expand CRUD APIs for Courses, Enrollments, Lessons, Assignments, Quizzes.
- Role/permission policies per endpoint.
- Notifications, live sessions, analytics, payments.

---

MIT License © Smart LMS API
