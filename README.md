# Queued Weather Demo (Laravel)

A **weather-request web app**: you enter a city name, submit, and the app fetches (or simulates) weather data in the background and shows you the result—temperature, condition, and stored JSON. It’s built with Laravel to demonstrate **agentic coding practices** and **meaningful background job processing** (queued jobs, status tracking, separation of concerns). The application uses simulated data by default; real API support is available when `WEATHER_API_KEY` is set. This repo was created for a technical interview assignment.

## What this app demonstrates

- **Background jobs**: weather fetching and JSON formatting happen in a queued job, not in the HTTP request.
- **Status tracking**: `pending → processing → completed/failed` stored in the database.
- **Persistence**: raw API JSON + formatted output are stored for review.
- **Separation of concerns**: view decision logic is centralized in a small presenter/helper used by Blade templates.
- **Automated tests**: includes a job test covering the simulated (no API key) path.

## Architecture (high-level)

- **Request flow**:
  - `POST /weather` creates a `weather_requests` row (`status=pending`) and dispatches a job.
  - A queue worker processes `FetchAndProcessWeatherJob`, updates the row, and marks completion/failure.
- **Queue driver**: `database` (so jobs are visible in the `jobs` table)
- **Database**: SQLite (simple local setup)

### Key files

- **Routes**: `routes/web.php`
- **Controller**: `app/Http/Controllers/WeatherController.php`
- **Job**: `app/Jobs/FetchAndProcessWeatherJob.php`
- **Model**: `app/Models/WeatherRequest.php`
- **Migration**: `database/migrations/2025_01_18_142503_create_weather_requests_table.php`
- **Views**:
  - `resources/views/weather/index.blade.php`
  - `resources/views/weather/show.blade.php`

## AI tool + agentic workflow (assignment requirement)

- **AI tool used**: Cursor (agentic coding in-repo)
- **How it was used**:
  - Planned the queue-centric architecture first (job responsibilities, status tracking, schema).
  - Generated code incrementally (migration → model → controller/routes → job → views).
  - Debugged environment issues (Windows permissions) based on runtime errors, not guesswork.
- **Decision-making**: I stayed in control of trade-offs (database queue vs redis; simulated mode vs requiring API keys; minimal service abstraction for a small demo).

## Running the app locally

### Prerequisites

- PHP 8.2+
- Composer
- (Optional) Node + npm if you want to run Vite, but the app also has a CSS fallback so UI still works without a frontend build.

### Setup (all platforms)

```powershell
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate
```

### Start the app (two terminals)

Terminal 1 (web server):

```powershell
php artisan serve
```

Terminal 2 (queue worker):

```powershell
php artisan queue:work --tries=1 --sleep=1
```

Leave the queue worker running — otherwise requests will remain **pending** (jobs won’t be processed).

Then open:

- `http://127.0.0.1:8000/weather`

## Running tests

```powershell
php artisan test
```

## Weather API behavior

This project supports two modes:

- **Real API mode** (OpenWeatherMap): set an API key in `.env`:
  - `WEATHER_API_KEY=your_key_here`
- **Simulated mode** (default): if `WEATHER_API_KEY` is not set, the job generates a small simulated response and still stores it in the database. This keeps the demo runnable without external setup.

## Testing the queued workflow (manual)

- Visit `http://127.0.0.1:8000/weather`
- Enter a city (e.g. "London") and submit
- You’ll be redirected to a details page:
  - Status should move **pending → processing → completed**
  - On completion you’ll see a summary + raw JSON stored by the job

## Windows notes (important)

On Windows, you may hit permissions/attributes issues that cause Laravel/SQLite errors.

### Fix 1: `bootstrap/cache` must be writable

Run in PowerShell (from the project root):

```powershell
attrib -R bootstrap\cache /S /D
icacls bootstrap\cache /grant Everyone:F /T
```

### Fix 2: SQLite “disk I/O error” (folder marked read-only)

If you see errors like `SQLSTATE[HY000]: General error: 10 disk I/O error`, ensure the `database` folder is not read-only and is writable:

```powershell
attrib -R database /S /D
icacls database /grant Everyone:F /T
```

## Interview discussion points

- **Why a queued job here**: external API calls and processing shouldn’t block the HTTP request.
- **Why database queue**: simplest to demonstrate jobs + inspect the `jobs` table without extra infrastructure.
- **Failure handling**: job catches exceptions and persists `error_message` with `status=failed`.
- **Prompting techniques that worked**:
  - Ask for a plan + trade-offs before writing code
  - Request small incremental changes, then run and verify
  - Use runtime errors as feedback loops and document fixes

## Repository hygiene (what is NOT committed)

This repo intentionally does **not** include:
- `.env` (secrets / local config)
- `vendor/`, `node_modules/`
- `database/database.sqlite` (local data)
- runtime files under `storage/`

Reviewers can recreate everything with the setup steps above.
