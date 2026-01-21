<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This method is called when we run: php artisan migrate
     * It creates the weather_requests table in our database
     */
    public function up(): void
    {
        // Create a table called 'weather_requests'
        // This table will store all weather requests and their results
        Schema::create('weather_requests', function (Blueprint $table) {
            // id() creates an 'id' column that auto-increments (1, 2, 3, ...)
            // This is the primary key - a unique identifier for each row
            $table->id();
            
            // string() creates a text column for storing the city/location name
            // Example: "London", "New York"
            $table->string('location');
            
            // text() stores the raw JSON response from the weather API
            // nullable() means this can be empty (NULL) - it won't have data until API responds
            $table->text('raw_json')->nullable();
            
            // text() stores the formatted JSON (after we process it)
            // nullable() because it won't exist until processing is done
            $table->text('formatted_data')->nullable();
            
            // string() stores just the temperature (extracted from JSON)
            // Example: "15°C", "72°F"
            $table->string('temperature')->nullable();
            
            // string() stores the weather condition (extracted from JSON)
            // Example: "Clear", "Rainy", "Cloudy"
            $table->string('condition')->nullable();
            
            // string() stores the current status of the request
            // Possible values: 'pending', 'processing', 'completed', 'failed'
            $table->string('status')->default('pending');
            
            // text() stores error message if something goes wrong
            // nullable() because there might not be an error
            $table->text('error_message')->nullable();
            
            // timestamps() automatically creates two columns:
            // - created_at: when the record was created
            // - updated_at: when the record was last updated
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * This method is called when we run: php artisan migrate:rollback
     * It removes the weather_requests table from the database
     */
    public function down(): void
    {
        // Drop (delete) the weather_requests table if it exists
        Schema::dropIfExists('weather_requests');
    }
};
