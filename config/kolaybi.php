<?php

return [

    /*
    |--------------------------------------------------------------------------
    | KolayBi API Key
    |--------------------------------------------------------------------------
    |
    | The API key provided by the KolayBi developer portal. This key is
    | required for authenticating all your API requests.
    |
    */

    'api_key' => env('KOLAYBI_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | KolayBi Channel ID
    |--------------------------------------------------------------------------
    |
    | The unique identifier for the communication channel. This determines
    | through which channel the invoice and customer records will be processed.
    |
    */

    'channel_id' => env('KOLAYBI_CHANNEL_ID'),

    /*
    |--------------------------------------------------------------------------
    | Sandbox Mode
    |--------------------------------------------------------------------------
    |
    | This option determines whether the application should run in the
    | test environment or production environment. Set to true for testing.
    |
    */

    'sandbox' => (bool) env('KOLAYBI_SANDBOX', false),

    /*
    |--------------------------------------------------------------------------
    | API Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL for the API requests. It first checks the environment
    | variable. If not set, it defaults to the sandbox or production URL
    | based on the sandbox setting above.
    |
    */

    'base_url' => env('KOLAYBI_BASE_URL', env('KOLAYBI_SANDBOX', false)
        ? 'https://ofis-sandbox-api.kolaybi.com'
        : 'https://ofis-api.kolaybi.com'),

    /*
    |--------------------------------------------------------------------------
    | HTTP Options
    |--------------------------------------------------------------------------
    |
    | Request timeout in seconds and retry behaviour for transient
    | connection failures and 5xx responses.
    |
    */

    'timeout' => (int) env('KOLAYBI_TIMEOUT', 30),

    'connect_timeout' => (int) env('KOLAYBI_CONNECT_TIMEOUT', 10),

    'retry' => [
        'times' => (int) env('KOLAYBI_RETRY_TIMES', 0),
        'sleep' => (int) env('KOLAYBI_RETRY_SLEEP', 200),
    ],

    /*
    |--------------------------------------------------------------------------
    | Token Cache
    |--------------------------------------------------------------------------
    |
    | The bearer token is cached to avoid authenticating on every request.
    | You may choose a dedicated cache store and the token lifetime here.
    |
    */

    'cache' => [
        'store' => env('KOLAYBI_CACHE_STORE'),
        'prefix' => env('KOLAYBI_CACHE_PREFIX', 'kolaybi_bearer_token_'),
        'ttl' => (int) env('KOLAYBI_TOKEN_TTL', 82800),
    ],

];
