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
 * Background job that fetches weather data and stores both raw + formatted output.
 *
 * Behavior:
 * - If `WEATHER_API_KEY` is set: calls OpenWeatherMap.
 * - Otherwise: uses a small simulated response (keeps the demo runnable without external setup).
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
        $this->weatherRequest->update([
            'status' => 'processing',
            'error_message' => null,
        ]);

        try {
            $apiKey = env('WEATHER_API_KEY');

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
                throw new \RuntimeException('Weather API request failed with status '.$response->status());
            }

            $data = $response->json();

            $temperature = $data['main']['temp'] ?? null;
            $condition = $data['weather'][0]['description'] ?? 'Unknown';
            $cityName = $data['name'] ?? $this->weatherRequest->location;

            $summary = $temperature !== null
                ? "In {$cityName}, it is {$temperature}°C with {$condition}."
                : "Weather data for {$cityName}: {$condition}.";

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
            $this->weatherRequest->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}

