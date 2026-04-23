<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    'base_url' => env('USPTO_BASE_URL', 'https://api.uspto.gov'),
    'timeout' => (int) env('USPTO_TIMEOUT', 120),
    'connect_timeout' => (int) env('USPTO_CONNECT_TIMEOUT', 10),
    'verify' => env('USPTO_VERIFY_SSL', true),
    'headers' => [
        'Accept' => 'application/json',
        'X-API-KEY' => env('USPTO_API_KEY'),
    ],
];
