<?php

namespace Theposeidonas\Kolaybi\Tests\Unit;

use Illuminate\Support\Facades\Http;
use Theposeidonas\Kolaybi\Enums\Currency;
use Theposeidonas\Kolaybi\Enums\VATRate;
use Theposeidonas\Kolaybi\Exceptions\KolaybiApiException;
use Theposeidonas\Kolaybi\Exceptions\KolaybiValidationException;
use Theposeidonas\Kolaybi\Facades\Kolaybi;
use Theposeidonas\Kolaybi\Tests\TestCase;

class ProformaResourceTest extends TestCase
{
    public function test_create_success()
    {
        Http::fake([
            '*/kolaybi/v1/proformas' => Http::response([
                'success' => true,
                'data' => ['id' => 700]
            ], 201)
        ]);

        $response = Kolaybi::proforma()->create([
            'contact_id' => 1,
            'address_id' => 1,
            'order_date' => '2026-01-28',
            'currency' => Currency::values()[0],
            'items' => [
                [
                    'product_id' => 1,
                    'quantity' => '5',
                    'unit_price' => '200',
                    'vat_rate' => VATRate::values()[0]
                ]
            ]
        ]);

        $this->assertTrue($response->isSuccess());
        $this->assertEquals(700, $response->getData()['id']);
    }

    public function test_create_fail()
    {
        $this->expectException(KolaybiValidationException::class);

        Kolaybi::proforma()->create([
            'contact_id' => 1
        ]);
    }

    public function test_find_success()
    {
        Http::fake([
            '*/kolaybi/v1/proformas/700' => Http::response([
                'success' => true,
                'data' => ['id' => 700, 'description' => 'Proforma Detayı']
            ], 200)
        ]);

        $response = Kolaybi::proforma()->find(700);

        $this->assertTrue($response->isSuccess());
        $this->assertEquals(700, $response->getData()['id']);
    }

    public function test_find_fail()
    {
        Http::fake([
            '*/kolaybi/v1/proformas/999' => Http::response([
                'success' => false,
                'message' => 'Proforma bulunamadı'
            ], 404)
        ]);

        $this->expectException(KolaybiApiException::class);
        $this->expectExceptionCode(404);

        Kolaybi::proforma()->find(999);
    }
}