<?php

namespace Theposeidonas\Kolaybi\Tests;

use Illuminate\Support\Facades\Http;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Theposeidonas\Kolaybi\KolaybiServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Http::fake([
            '*/access_token' => Http::response([
                'success' => true,
                'data' => 'mock-token',
            ], 200),
        ]);
        if (file_exists(__DIR__.'/../.env')) {
            \Dotenv\Dotenv::createImmutable(__DIR__.'/../')->load();
        }

        config()->set('kolaybi.api_key', env('KOLAYBI_API_KEY'));
        config()->set('kolaybi.channel_id', env('KOLAYBI_CHANNEL_ID'));
        config()->set('kolaybi.is_sandbox', env('KOLAYBI_SANDBOX', true));
        config()->set('kolaybi.base_url', env('KOLAYBI_BASE_URL', 'https://ofis-sandbox-api.kolaybi.com'));
    }

    protected function getPackageProviders($app): array
    {
        return [
            KolaybiServiceProvider::class,
        ];
    }
}
