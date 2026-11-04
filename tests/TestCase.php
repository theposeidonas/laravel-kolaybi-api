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

        Http::preventStrayRequests();

        Http::fake([
            '*/access_token' => Http::response([
                'success' => true,
                'data' => 'mock-token',
            ], 200),
        ]);
    }

    protected function getPackageProviders($app): array
    {
        return [
            KolaybiServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('cache.default', 'array');
        $app['config']->set('kolaybi.api_key', 'test-api-key');
        $app['config']->set('kolaybi.channel_id', 'test-channel');
        $app['config']->set('kolaybi.sandbox', true);
        $app['config']->set('kolaybi.base_url', 'https://ofis-sandbox-api.kolaybi.com');
    }
}
