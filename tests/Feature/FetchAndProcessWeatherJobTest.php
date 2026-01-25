<?php

namespace Tests\Feature;

use App\Jobs\FetchAndProcessWeatherJob;
use App\Models\WeatherRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FetchAndProcessWeatherJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_completes_with_simulated_data_when_no_api_key_is_set(): void
    {
        // Ensure we are in "simulated" mode for this test.
        putenv('WEATHER_API_KEY=');
        $_ENV['WEATHER_API_KEY'] = '';
        $_SERVER['WEATHER_API_KEY'] = '';

        $weatherRequest = WeatherRequest::create([
            'location' => 'London',
            'status' => 'pending',
        ]);

        (new FetchAndProcessWeatherJob($weatherRequest))->handle();

        $weatherRequest->refresh();

        $this->assertSame('completed', $weatherRequest->status);
        $this->assertNotNull($weatherRequest->raw_json);
        $this->assertNotNull($weatherRequest->formatted_data);
        $this->assertStringContainsString('London', $weatherRequest->raw_json);
        $this->assertStringContainsString('simulated', $weatherRequest->formatted_data);
    }
}

