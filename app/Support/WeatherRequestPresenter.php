<?php

namespace App\Support;

use App\Models\WeatherRequest;

/**
 * Presentation helpers for WeatherRequest rendering.
 *
 * Goal: keep view templates focused on layout rather than branching/formatting logic.
 */
final class WeatherRequestPresenter
{
    public static function isInProgress(WeatherRequest $weatherRequest): bool
    {
        return in_array($weatherRequest->status, ['pending', 'processing'], true);
    }

    public static function isFailed(WeatherRequest $weatherRequest): bool
    {
        return $weatherRequest->status === 'failed';
    }

    public static function isCompleted(WeatherRequest $weatherRequest): bool
    {
        return $weatherRequest->status === 'completed';
    }

    public static function statusColor(string $status): string
    {
        return match ($status) {
            'pending' => '#f97316',
            'processing' => '#eab308',
            'completed' => '#22c55e',
            'failed' => '#ef4444',
            default => '#9ca3af',
        };
    }

    /**
     * Decode the formatted JSON (if present and valid) into an array.
     */
    public static function formatted(WeatherRequest $weatherRequest): ?array
    {
        $json = $weatherRequest->formatted_data;
        if (! is_string($json) || $json === '') {
            return null;
        }

        $decoded = json_decode($json, true);

        return is_array($decoded) ? $decoded : null;
    }

    public static function summary(WeatherRequest $weatherRequest): ?string
    {
        $formatted = self::formatted($weatherRequest);

        return is_array($formatted) ? ($formatted['summary'] ?? null) : null;
    }

    public static function temperatureForDisplay(WeatherRequest $weatherRequest): string
    {
        $formatted = self::formatted($weatherRequest);
        $value = is_array($formatted) ? ($formatted['temperature'] ?? null) : null;
        $value ??= $weatherRequest->temperature;

        return $value ?? 'n/a';
    }

    public static function conditionForDisplay(WeatherRequest $weatherRequest): string
    {
        $formatted = self::formatted($weatherRequest);
        $value = is_array($formatted) ? ($formatted['condition'] ?? null) : null;
        $value ??= $weatherRequest->condition;

        return $value ?? 'n/a';
    }
}

