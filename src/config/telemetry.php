<?php

return [

    /**
     * Enable the package to log to Telemetry.
     */
    'enabled' => env('TELEMETRY_ENABLED', true),

    /**
     * The source token found in the BetterStack console.
     */
    'sourceToken' => env('TELEMETRY_SOURCE_TOKEN'),

    /**
     * List of exceptions you would like the telemetry logger to ignore.
     * Acts as an exception blacklist.
     */
    'exception_blacklist' => [
        // Illuminate\Validation\ValidationException::class,
    ],

    /**
     * List of exceptions you would like to report.
     * Acts as an exception whitelist.
     */
    'exception_whitelist' => [
        // Illuminate\Validation\ValidationException::class,
    ],
];