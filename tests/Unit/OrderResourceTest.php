<?php

namespace Theposeidonas\Kolaybi\Tests\Unit;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Theposeidonas\Kolaybi\Exceptions\KolaybiApiException;
use Theposeidonas\Kolaybi\Exceptions\KolaybiValidationException;
use Theposeidonas\Kolaybi\Facades\Kolaybi;
use Theposeidonas\Kolaybi\Tests\TestCase;

class OrderResourceTest extends TestCase
{
    public function test_create_success()
    {
        Http::fake([
            '*/access_token' => Http::response(['success' => true, 'data' => 'token'], 200),
            '*/kolaybi/v1/orders' => Http::response([
                'success' => true,
                'data' => ['id' => 500]
            ], 201)
        ]);

        $response = Kolaybi::order()->create([
            'contact_id' => 1,
            'address_id' => 1,
            'order_date' => '2026-01-28',
            'currency' => 'TRY',
            'items' => [
                [
                    'product_id' => 1,
                    'quantity' => '10',
                    'unit_price' => '50.5',
                    'vat_rate' => 20
                ]
            ]
        ]);

        $this->assertTrue($response->isSuccess());
        $this->assertEquals(500, $response->getData()['id']);
    }

    /**
     * @throws KolaybiApiException
     * @throws ConnectionException
     */
    public function test_create_fail()
    {
        $this->expectException(KolaybiValidationException::class);

        Kolaybi::order()->create([
            'contact_id' => 1
        ]);
    }

    public function test_find_success()
    {
        Http::fake([
            '*/access_token' => Http::response(['success' => true, 'data' => 'token'], 200),
            '*/kolaybi/v1/orders/500' => Http::response([
                'success' => true,
                'data' => ['id' => 500, 'description' => 'Test Sipariş']
            ], 200)
        ]);

        $response = Kolaybi::order()->find(500);

        $this->assertTrue($response->isSuccess());
        $this->assertEquals(500, $response->getData()['id']);
    }

    /**
     * @throws ConnectionException
     */
    public function test_find_fail()
    {
        Http::fake([
            '*/access_token' => Http::response(['success' => true, 'data' => 'token'], 200),
            '*/kolaybi/v1/orders/999' => Http::response([
                'success' => false,
                'message' => 'Sipariş bulunamadı'
            ], 404)
        ]);

        $this->expectException(KolaybiApiException::class);
        $this->expectExceptionCode(404);

        Kolaybi::order()->find(999);
    }
}