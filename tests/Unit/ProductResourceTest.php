<?php

namespace Theposeidonas\Kolaybi\Tests\Unit;

use Illuminate\Support\Facades\Http;
use Theposeidonas\Kolaybi\Exceptions\KolaybiApiException;
use Theposeidonas\Kolaybi\Exceptions\KolaybiValidationException;
use Theposeidonas\Kolaybi\Facades\Kolaybi;
use Theposeidonas\Kolaybi\Tests\TestCase;

class ProductResourceTest extends TestCase
{
    public function test_list_success()
    {
        Http::fake([
            '*/kolaybi/v1/products*' => Http::response([
                'success' => true,
                'data' => [['id' => 1, 'name' => 'MacBook Air']]
            ], 200)
        ]);

        $response = Kolaybi::product()->list(['name' => 'MacBook']);

        $this->assertTrue($response->isSuccess());
        $this->assertCount(1, $response->getData());
    }

    public function test_list_fail()
    {
        $this->expectException(KolaybiValidationException::class);

        Kolaybi::product()->list(['type' => 'invalid_enum_value']);
    }

    public function test_create_success()
    {
        Http::fake([
            '*/kolaybi/v1/products' => Http::response([
                'success' => true,
                'data' => ['id' => 10, 'name' => 'Tesla Model Y']
            ], 201)
        ]);

        $response = Kolaybi::product()->create([
            'name' => 'Tesla Model Y',
            'price' => 2000000,
            'price_currency' => \Theposeidonas\Kolaybi\Enums\Currency::values()[0]
        ]);

        $this->assertTrue($response->isSuccess());
        $this->assertEquals(10, $response->getData()['id']);
    }

    public function test_create_fail()
    {
        $this->expectException(KolaybiValidationException::class);

        Kolaybi::product()->create([
            'code' => 'P-001'
        ]);
    }

    public function test_find_success()
    {
        Http::fake([
            '*/kolaybi/v1/products/10' => Http::response([
                'success' => true,
                'data' => ['id' => 10, 'name' => 'Tesla Model Y']
            ], 200)
        ]);

        $response = Kolaybi::product()->find(10);

        $this->assertTrue($response->isSuccess());
        $this->assertEquals('Tesla Model Y', $response->getData()['name']);
    }

    public function test_find_fail()
    {
        Http::fake([
            '*/kolaybi/v1/products/999' => Http::response([
                'success' => false,
                'message' => 'Product not found'
            ], 404)
        ]);

        $this->expectException(KolaybiApiException::class);
        $this->expectExceptionCode(404);

        Kolaybi::product()->find(999);
    }

    public function test_update_success()
    {
        Http::fake([
            '*/kolaybi/v1/products/10' => Http::response([
                'success' => true,
                'data' => ['id' => 10, 'name' => 'Tesla Model Y Juniper']
            ], 200)
        ]);

        $response = Kolaybi::product()->update(10, [
            'name' => 'Tesla Model Y Juniper'
        ]);

        $this->assertTrue($response->isSuccess());
        $this->assertEquals('Tesla Model Y Juniper', $response->getData()['name']);
    }

    public function test_update_fail()
    {
        $this->expectException(KolaybiValidationException::class);

        Kolaybi::product()->update(10, [
            'price' => 'not-numeric'
        ]);
    }

    public function test_stock_success()
    {
        Http::fake([
            '*/kolaybi/v1/stock_histories' => Http::response([
                'success' => true,
                'data' => ['id' => 1, 'quantity' => 5]
            ], 201)
        ]);

        $response = Kolaybi::product()->stock([
            'product_id' => 10,
            'quantity' => 5,
            'stock_flow_direction' => 1
        ]);

        $this->assertTrue($response->isSuccess());
    }

    public function test_stock_fail()
    {
        $this->expectException(KolaybiValidationException::class);

        Kolaybi::product()->stock([
            'product_id' => 10,
            'quantity' => 5,
            'stock_flow_direction' => 0
        ]);
    }
}