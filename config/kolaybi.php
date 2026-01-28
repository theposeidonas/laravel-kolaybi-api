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

    'sandbox' => env('KOLAYBI_SANDBOX', false),

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

];
