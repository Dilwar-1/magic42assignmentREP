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

## Laravel File Structure

```
phpapp/
├── app/                          # Your application code
│   ├── Http/Controllers/         # Controllers (handle requests)
│   ├── Jobs/                     # Queue jobs (background tasks)
│   └── Models/                   # Models (database tables)
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
