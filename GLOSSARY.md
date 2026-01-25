# Terminology Guide - Simple Explanations

This document explains key terms as we encounter them. I'll add to it as we go!

## Basic Concepts We'll Use

### Database
- **What it is**: A place to store data (like a spreadsheet, but more powerful)
- **In our project**: We'll store weather requests and their results here
- **Laravel uses**: SQLite (a simple file-based database - no setup needed!)

### Database Table
- **What it is**: Like a spreadsheet with columns and rows
- **Example**: A "weather_requests" table might have columns: id, location, status, temperature
- **Each row**: One weather request

### Migration
- **What it is**: A file that creates or modifies database tables
- **Why use it**: Keeps track of database changes - you can run it again anytime
- **File location**: `database/migrations/`

### Model
- **What it is**: A PHP class that represents a database table
- **Example**: `WeatherRequest` model represents the `weather_requests` table
- **What it does**: Lets us easily work with database records in PHP code
- **File location**: `app/Models/`

### Controller
- **What it is**: A PHP class that handles user requests (like form submissions)
- **What it does**: Receives input, processes it, returns results
- **Example**: `WeatherController` handles weather form submissions
- **File location**: `app/Http/Controllers/`

### Route
- **What it is**: Maps a URL to a controller method
- **Example**: `/weather` URL → `WeatherController@index` method
- **File location**: `routes/web.php`

### View
- **What it is**: HTML template that displays content to users
- **Example**: The page where users enter a city name
- **File location**: `resources/views/`

### Queue Job
- **What it is**: A task that runs in the background (not immediately)
- **Why use it**: API calls can take time - we don't want users waiting
- **Example**: Fetch weather data from API in the background
- **File location**: `app/Jobs/`

### Queue Worker
- **What it is**: A process that runs queue jobs
- **What it does**: Continuously checks for jobs and processes them
- **Command**: `php artisan queue:work`

### Queue Driver (Database Queue)
- **What it is**: The storage backend Laravel uses to hold queued jobs until a worker processes them.
- **In our project**: `QUEUE_CONNECTION=database`, so jobs are stored in the `jobs` table.
- **Why it’s good for a demo**: easy to run locally and you can inspect jobs in the DB.

### Status Tracking (pending → processing → completed/failed)
- **What it is**: A simple state machine stored in the database so you can see what stage a request is in.
- **In our project**: `WeatherRequest.status` changes as the job runs.

### Presenter / Helper (Separation of Concerns)
- **What it is**: A small class used by the view layer to keep templates focused on rendering, not decision logic.
- **In our project**: `app/Support/WeatherRequestPresenter.php` contains helpers like:
  - status grouping (pending/processing/etc.)
  - status color mapping
  - decoding stored formatted JSON for display

### Environment Variables (.env)
- **What it is**: Configuration values loaded at runtime (not committed to git).
- **In our project**:
  - `QUEUE_CONNECTION=database` controls the queue driver
  - `DB_CONNECTION=sqlite` controls the database

### WEATHER_API_KEY (Real API vs Simulated Mode)
- **What it is**: An optional API key used to call the real OpenWeatherMap API.
- **If set**: the job makes a real HTTP request to OpenWeatherMap and stores the response.
- **If not set**: the job uses a small simulated payload (still stored in the database) so the app runs without external setup.

## Laravel File Structure

```
phpapp/
├── app/                          # Your application code
│   ├── Http/Controllers/         # Controllers (handle requests)
│   ├── Jobs/                     # Queue jobs (background tasks)
│   └── Models/                   # Models (database tables)
│   └── Support/                  # Presenter/helpers for views
│
├── database/
│   └── migrations/               # Database table definitions
│
├── routes/
│   └── web.php                   # URL routes
│
└── resources/views/              # HTML templates
```

---

*This glossary will grow as we add more concepts!*
