# Setup Guide - Getting Your Weather App Running

## The Issue We're Having

Laravel is complaining that `bootstrap/cache` must be writable. This is likely because:
- Your project is in OneDrive (which can cause permission issues)
- Laravel needs to write cache files there

## Quick Fix Options

### Option 1: Run Migrations Manually (Try This First)

Open PowerShell in your project folder and try:

```powershell
cd c:\Users\dilwa\OneDrive\Documents\magic42assignment\phpapp
php artisan migrate
```

If that still fails, try:

```powershell
# Make sure cache directory is writable
icacls "bootstrap\cache" /grant Everyone:F
php artisan migrate
```

### Option 2: Create Database Manually

If migrations keep failing, you can create the database file manually:

1. Create an empty file: `database\database.sqlite`
2. Then try migrations again

### Option 3: Use a Different Location (If OneDrive is the problem)

Move your project outside OneDrive temporarily:
- Copy the entire `phpapp` folder to `C:\projects\phpapp` (or anywhere outside OneDrive)
- Try running migrations there
- Move it back when done

## What We've Built (Summary)

✅ **Database Migration** - Creates `weather_requests` table  
✅ **Model** - `WeatherRequest` class  
✅ **Queue Job** - `FetchAndProcessWeatherJob` (the background worker)  
✅ **Controller** - `WeatherController` (handles web requests)  
✅ **Routes** - URLs connected to controller  
✅ **Views** - Web pages (form + results)  

## Once Migrations Work

After migrations succeed, you'll need to:

1. **Start the web server:**
   ```powershell
   php artisan serve
   ```
   Then visit: http://localhost:8000

2. **Start the queue worker** (in a separate terminal):
   ```powershell
   php artisan queue:work
   ```
   This processes background jobs!

3. **Test the app:**
   - Go to http://localhost:8000
   - Enter a city name (e.g., "London")
   - Click "Queue weather fetch"
   - Watch the status change from "pending" → "processing" → "completed"

## What to Do Right Now

**Don't worry about the migration issue!** 

We can:
1. **Do the full walkthrough now** - I'll explain everything we built
2. **You can fix migrations later** - Try the options above when you have time
3. **The code is complete** - Everything is ready, just needs the database created

**Which would you prefer?**
