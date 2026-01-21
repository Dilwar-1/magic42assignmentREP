<?php

namespace App\Jobs;

use App\Models\WeatherRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * FetchAndProcessWeatherJob
 *
 * Simple explanation:
 * - This is the **background task**.
 * - It will be added to a queue and run by a
 *   separate worker process, not directly in
 *   the user's web request.
 *
 * What it does:
 * 1. Calls a weather API for the given location.
 * 2. Stores the raw JSON response.
 * 3. Extracts key pieces of data (e.g. temperature, condition).
 * 4. Saves a formatted version into the database.
 * 5. Updates the status: pending → processing → completed / failed.
 */
class FetchAndProcessWeatherJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The WeatherRequest instance this job is working on.
     */
    public WeatherRequest $weatherRequest;

    /**
     * Create a new job instance.
     *
     * Simple explanation:
     * - When we dispatch this job, we pass in the WeatherRequest.
     * - Laravel will serialize it (turn it into a storable form)
     *   and later unserialize it when the job runs.
     */
    public function __construct(WeatherRequest $weatherRequest)
    {
        $this->weatherRequest = $weatherRequest;
    }

    /**
     * Execute the job.
     *
     * This method is called by the queue worker.
     */
    public function handle(): void
    {
        // Mark as processing
        $this->weatherRequest->update([
            'status' => 'processing',
            'error_message' => null,
        ]);

        try {
            // 1. Call the weather API
            //
            // For now we assume you'll set an API key in your .env file:
            // WEATHER_API_KEY=your_key_here
            //
            // We'll use OpenWeatherMap as an example:
            // https://api.openweathermap.org/data/2.5/weather?q=London&appid=KEY&units=metric

            $apiKey = env('WEATHER_API_KEY');

            // If no API key is set, we can simulate a response
            if (empty($apiKey)) {
                $simulated = [
                    'location' => $this->weatherRequest->location,
                    'temperature' => 20,
                    'condition' => 'Clear (simulated - no API key set)',
                ];

                $this->weatherRequest->update([
                    'raw_json' => json_encode($simulated),
                    'formatted_data' => json_encode([
                        'location' => $simulated['location'],
                        'summary' => "It is {$simulated['temperature']}°C and {$simulated['condition']}.",
                    ], JSON_PRETTY_PRINT),
                    'temperature' => $simulated['temperature'] . '°C',
                    'condition' => $simulated['condition'],
                    'status' => 'completed',
                ]);

                return;
            }

            $response = Http::get('https://api.openweathermap.org/data/2.5/weather', [
                'q' => $this->weatherRequest->location,
                'appid' => $apiKey,
                'units' => 'metric',
            ]);

            if (! $response->successful()) {
                // Handle non-200 responses
                throw new \RuntimeException('Weather API request failed with status '.$response->status());
            }

            $data = $response->json();

            // Safely pull values from the JSON (with defaults)
            $temperature = $data['main']['temp'] ?? null;
            $condition = $data['weather'][0]['description'] ?? 'Unknown';
            $cityName = $data['name'] ?? $this->weatherRequest->location;

            // Build a human-friendly summary
            $summary = $temperature !== null
                ? "In {$cityName}, it is {$temperature}°C with {$condition}."
                : "Weather data for {$cityName}: {$condition}.";

            // 2. Update the WeatherRequest record with processed data
            $this->weatherRequest->update([
                'raw_json' => json_encode($data),
                'formatted_data' => json_encode([
                    'location' => $cityName,
                    'summary' => $summary,
                    'temperature' => $temperature,
                    'condition' => $condition,
                ], JSON_PRETTY_PRINT),
                'temperature' => $temperature !== null ? $temperature.'°C' : null,
                'condition' => $condition,
                'status' => 'completed',
            ]);
        } catch (Throwable $e) {
            // 3. If anything goes wrong, record the error and mark as failed
            $this->weatherRequest->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}

