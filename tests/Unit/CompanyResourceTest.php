<?php

namespace Theposeidonas\Kolaybi\Tests\Unit;

use Illuminate\Support\Facades\Http;
use Theposeidonas\Kolaybi\Exceptions\KolaybiApiException;
use Theposeidonas\Kolaybi\Facades\Kolaybi;
use Theposeidonas\Kolaybi\Tests\TestCase;

class CompanyResourceTest extends TestCase
{
    public function test_it_can_list_companies_successfully()
    {
        Http::fake([
            '*/kolaybi/v1/companies' => Http::response([
                'success' => true,
                'data' => [
                    [
                        'company_id' => 5,
                        'company_name' => 'KolayBi',
                        'identity_no' => '01234567890',
                        'tax_office' => 'FEKE MAL MÜDÜRLÜĞÜ',
                        'company_currency' => 'try',
                        'address' => [
                            'city' => 'Aydın',
                            'district' => 'Efeler',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = Kolaybi::company()->list();

        $this->assertTrue($response->isSuccess());
        $this->assertIsArray($response->getData());
        $this->assertEquals('KolayBi', $response->getData()[0]['company_name']);
        $this->assertEquals('Aydın', $response->getData()[0]['address']['city']);
    }

    public function test_it_throws_api_exception_when_companies_list_fails()
    {
        Http::fake([
            '*/kolaybi/v1/companies' => Http::response([
                'success' => false,
                'message' => 'Yetkisiz erişim veya sunucu hatası',
            ], 401),
        ]);

        $this->expectException(KolaybiApiException::class);
        $this->expectExceptionCode(401);

        Kolaybi::company()->list();
    }
}
