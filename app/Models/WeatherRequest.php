<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * WeatherRequest model
 *
 * Simple explanation:
 * - This class represents one row in the `weather_requests` table.
 * - Instead of writing SQL by hand, we use this model to
 *   create, read, update, and delete records.
 */
class WeatherRequest extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     * Laravel would guess this from the class name,
     * but we set it explicitly to be very clear.
     */
    protected $table = 'weather_requests';

    /**
     * Which columns can be mass-assigned.
     *
     * Simple explanation:
     * - When we do WeatherRequest::create([...]),
     *   only these fields are allowed to be filled.
     * - This helps protect against accidentally
     *   writing to columns we didn't intend to.
     */
    protected $fillable = [
        'location',
        'raw_json',
        'formatted_data',
        'temperature',
        'condition',
        'status',
        'error_message',
    ];

    /**
     * Casts allow us to automatically convert
     * columns to certain PHP types when we read them.
     */
    protected $casts = [
        // If we want to treat formatted_data as an array later,
        // we can change this to 'array'. For now we keep it text.
        'raw_json' => 'string',
        'formatted_data' => 'string',
    ];
}

