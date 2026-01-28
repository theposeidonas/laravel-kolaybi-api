<?php

namespace Theposeidonas\Kolaybi\Tests\Unit;

use Illuminate\Support\Facades\Http;
use Theposeidonas\Kolaybi\Exceptions\KolaybiApiException;
use Theposeidonas\Kolaybi\Exceptions\KolaybiValidationException;
use Theposeidonas\Kolaybi\Facades\Kolaybi;
use Theposeidonas\Kolaybi\Tests\TestCase;

class BankResourceTest extends TestCase
{
    public function test_it_can_list_vaults_successfully()
    {
        Http::fake([
            '*/kolaybi/v1/vaults*' => Http::response([
                'success' => true,
                'data' => [
                    ['id' => 1, 'name' => 'Ana Kasa', 'type' => 'SAFE_DEPOSIT', 'currency' => 'try'],
                    ['id' => 2, 'name' => 'Banka Hesabı', 'type' => 'BANK_ACCOUNT', 'currency' => 'try'],
                ],
            ], 200),
        ]);

        $response = Kolaybi::bank()->list(['currency' => 'try']);

        $this->assertTrue($response->isSuccess());
        $this->assertCount(2, $response->getData());
        $this->assertEquals('Ana Kasa', $response->getData()[0]['name']);
    }

    public function test_it_throws_validation_exception_for_invalid_list_parameters()
    {
        $this->expectException(KolaybiValidationException::class);

        Kolaybi::bank()->list(['type' => 'invalid_type']);
    }

    public function test_it_can_find_a_specific_vault()
    {
        Http::fake([
            '*/kolaybi/v1/vaults/12' => Http::response([
                'success' => true,
                'data' => [
                    ['id' => 12, 'name' => 'Ana Kasa', 'balance' => 123000],
                ],
            ], 200),
        ]);

        $response = Kolaybi::bank()->find(12);

        $this->assertTrue($response->isSuccess());
        $this->assertEquals(12, $response->getData()[0]['id']);
    }

    public function test_it_throws_api_exception_when_vault_not_found()
    {
        Http::fake([
            '*/kolaybi/v1/vaults/999' => Http::response([
                'success' => false,
                'message' => 'Kasa bulunamadı',
            ], 404),
        ]);

        $this->expectException(KolaybiApiException::class);
        $this->expectExceptionCode(404);

        Kolaybi::bank()->find(999);
    }

    public function test_it_can_fetch_vault_transactions()
    {
        Http::fake([
            '*/kolaybi/v1/vaults/1/transactions' => Http::response([
                'success' => true,
                'data' => [
                    'transactionables' => [
                        ['id' => 135, 'amount' => 3400, 'description' => 'Tahsilat'],
                    ],
                    'calculations' => [
                        ['amount' => 1000, 'currency' => 'try'],
                    ],
                ],
            ], 200),
        ]);

        $response = Kolaybi::bank()->transactions(1);

        $this->assertTrue($response->isSuccess());
        $this->assertArrayHasKey('transactionables', $response->getData());
        $this->assertEquals(3400, $response->getData()['transactionables'][0]['amount']);
    }

    public function test_it_throws_api_exception_on_server_error()
    {
        Http::fake([
            '*/kolaybi/v1/vaults/1/transactions' => Http::response([
                'success' => false,
                'message' => 'Internal Server Error',
            ], 500),
        ]);

        $this->expectException(KolaybiApiException::class);
        $this->expectExceptionCode(500);

        Kolaybi::bank()->transactions(1);
    }
}
