<?php

namespace App\Http\Controllers;

use App\Jobs\FetchAndProcessWeatherJob;
use App\Models\WeatherRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * WeatherController
 *
 * Simple explanation:
 * - This is the \"traffic controller\" for weather features.
 * - It receives web requests, talks to the model and job,
 *   and returns views (HTML pages).
 */
class WeatherController extends Controller
{
    /**
     * Show the main weather page with:
     * - A form to enter a city
     * - A list of recent requests
     */
    public function index(): View
    {
        $recentRequests = WeatherRequest::orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('weather.index', [
            'recentRequests' => $recentRequests,
        ]);
    }

    /**
     * Handle the form submission:
     * - Validate the city name
     * - Create a WeatherRequest row with status \"pending\"
     * - Dispatch the background job
     * - Redirect to a page showing the status for this request
     */
    public function store(Request $request): RedirectResponse
    {
        // Basic validation: require a non-empty string for location
        $validated = $request->validate([
            'location' => ['required', 'string', 'max:255'],
        ]);

        // Create a new WeatherRequest row in the database
        $weatherRequest = WeatherRequest::create([
            'location' => $validated['location'],
            'status' => 'pending',
        ]);

        // Dispatch the background job
        FetchAndProcessWeatherJob::dispatch($weatherRequest);

        // Redirect to the \"show\" page for this specific request
        return redirect()
            ->route('weather.show', $weatherRequest)
            ->with('status', 'Weather request queued for processing.');
    }

    /**
     * Show a single weather request:
     * - If status is pending/processing: show a message
     * - If completed: show formatted result
     * - If failed: show error
     */
    public function show(WeatherRequest $weatherRequest): View
    {
        return view('weather.show', [
            'weatherRequest' => $weatherRequest,
        ]);
    }
}

