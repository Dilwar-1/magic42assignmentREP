<?php

use App\Http\Controllers\WeatherController;
use Illuminate\Support\Facades\Route;

// Home page: redirect to the main weather page for now
Route::get('/', function () {
    return redirect()->route('weather.index');
});

// Weather routes
// GET /weather        -> show form + recent requests
// POST /weather       -> submit a new weather request
// GET /weather/{id}   -> show status/result for one request
Route::get('/weather', [WeatherController::class, 'index'])
    ->name('weather.index');

Route::post('/weather', [WeatherController::class, 'store'])
    ->name('weather.store');

Route::get('/weather/{weatherRequest}', [WeatherController::class, 'show'])
    ->name('weather.show');
