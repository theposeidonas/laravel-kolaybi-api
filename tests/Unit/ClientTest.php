<?php

namespace Theposeidonas\Kolaybi\Tests\Unit;

use DateTimeImmutable;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Theposeidonas\Kolaybi\Enums\Currency;
use Theposeidonas\Kolaybi\Enums\VATRate;
use Theposeidonas\Kolaybi\Exceptions\KolaybiApiException;
use Theposeidonas\Kolaybi\Exceptions\KolaybiValidationException;
use Theposeidonas\Kolaybi\Facades\Kolaybi;
use Theposeidonas\Kolaybi\KolaybiClient;
use Theposeidonas\Kolaybi\Tests\TestCase;

class ClientTest extends TestCase
{
    public function test_it_sends_channel_and_bearer_headers(): void
    {
        Http::fake([
            '*/kolaybi/v1/users' => Http::response(['success' => true, 'data' => []]),
        ]);

        Kolaybi::user()->list();

        Http::assertSent(fn (Request $request): bool => str_ends_with($request->url(), '/kolaybi/v1/users')
            && $request->hasHeader('Channel', 'test-channel')
            && $request->hasHeader('Authorization', 'Bearer mock-token'));
    }

    public function test_it_caches_the_token(): void
    {
        Http::fake([
            '*/kolaybi/v1/users' => Http::response(['success' => true, 'data' => []]),
        ]);

        Kolaybi::user()->list();
        Kolaybi::user()->list();

        Http::assertSentCount(3);
    }

    public function test_it_refreshes_token_on_unauthorized(): void
    {
        Http::swap(new Factory);
        Http::fake([
            '*/access_token' => Http::sequence()
                ->push(['success' => true, 'data' => 'expired-token'])
                ->push(['success' => true, 'data' => 'fresh-token']),
            '*/kolaybi/v1/users' => Http::sequence()
                ->push(['success' => false, 'message' => 'Unauthenticated'], 401)
                ->push(['success' => true, 'data' => [['id' => 1]]]),
        ]);

        $response = Kolaybi::user()->list();

        $this->assertTrue($response->isSuccess());
        Http::assertSent(fn (Request $request): bool => $request->hasHeader('Authorization', 'Bearer fresh-token'));
    }

    public function test_it_throws_when_authentication_fails(): void
    {
        Http::swap(new Factory);
        Http::fake([
            '*/access_token' => Http::response(['success' => false, 'message' => 'Invalid key'], 401),
        ]);

        $this->expectException(KolaybiApiException::class);
        $this->expectExceptionMessage('Authentication failed: Invalid key');

        Kolaybi::auth()->getToken();
    }

    public function test_it_throws_when_credentials_missing(): void
    {
        $client = new KolaybiClient(['base_url' => 'https://example.test', 'api_key' => null, 'channel_id' => null]);

        $this->expectException(KolaybiApiException::class);

        $client->auth()->getToken();
    }

    public function test_it_resolves_the_same_client_and_resource_instances(): void
    {
        $this->assertSame(app(KolaybiClient::class), app('kolaybi'));
        $this->assertSame(Kolaybi::invoice(), Kolaybi::invoice());
    }

    public function test_validation_exception_exposes_errors(): void
    {
        try {
            Kolaybi::invoice()->create([]);
            $this->fail('Validation exception was not thrown.');
        } catch (KolaybiValidationException $e) {
            $this->assertArrayHasKey('contact_id', $e->errors());
            $this->assertArrayHasKey('items', $e->errors());
        }
    }

    public function test_api_exception_exposes_response(): void
    {
        Http::fake([
            '*/kolaybi/v1/tags/5' => Http::response(['success' => false, 'error' => ['message' => 'Boom']], 400),
        ]);

        try {
            Kolaybi::tag()->find(5);
            $this->fail('API exception was not thrown.');
        } catch (KolaybiApiException $e) {
            $this->assertSame('Boom', $e->getMessage());
            $this->assertSame(400, $e->getStatus());
            $this->assertSame(['message' => 'Boom'], $e->getError());
        }
    }

    public function test_it_accepts_enum_instances(): void
    {
        Http::fake([
            '*/kolaybi/v1/proformas' => Http::response(['success' => true, 'data' => ['id' => 1]], 201),
        ]);

        Kolaybi::proforma()->create([
            'contact_id' => 1,
            'address_id' => 1,
            'order_date' => new DateTimeImmutable('2026-01-28'),
            'currency' => Currency::TRY,
            'items' => [
                ['product_id' => 1, 'quantity' => '1', 'unit_price' => '100', 'vat_rate' => VATRate::RATE_20],
            ],
        ]);

        Http::assertSent(fn (Request $request): bool => str_ends_with($request->url(), '/kolaybi/v1/proformas')
            && $request['currency'] === 'TRY'
            && $request['order_date'] === '2026-01-28'
            && $request['items'][0]['vat_rate'] === 20);
    }

    public function test_response_supports_array_access_and_serialization(): void
    {
        Http::fake([
            '*/kolaybi/v1/products/7' => Http::response(['success' => true, 'data' => ['id' => 7, 'meta' => ['code' => 'P7']]]),
        ]);

        $response = Kolaybi::product()->find(7);

        $this->assertSame(7, $response['id']);
        $this->assertSame('P7', $response->getData('meta.code'));
        $this->assertTrue(isset($response['meta']));
        $this->assertSame(['success' => true, 'data' => ['id' => 7, 'meta' => ['code' => 'P7']]], $response->toArray());
        $this->assertJson($response->toJson());
    }

    public function test_proforma_find_uses_post(): void
    {
        Http::fake([
            '*/kolaybi/v1/proformas/3' => Http::response(['success' => true, 'data' => ['id' => 3]]),
        ]);

        Kolaybi::proforma()->find(3);

        Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
            && str_ends_with($request->url(), '/kolaybi/v1/proformas/3'));
    }

    public function test_with_config_returns_isolated_client(): void
    {
        $client = Kolaybi::withConfig(['channel_id' => 'other']);

        $this->assertSame('other', $client->getConfig()['channel_id']);
        $this->assertSame('test-channel', Kolaybi::getConfig()['channel_id']);
    }

    public function test_validation_message_object_is_flattened(): void
    {
        Http::fake([
            '*/kolaybi/v1/products' => Http::response(['success' => false, 'message' => ['name' => ['Name is required']]], 422),
        ]);

        $this->expectException(KolaybiApiException::class);
        $this->expectExceptionMessage('Name is required');

        Kolaybi::post('/kolaybi/v1/products', []);
    }
}
