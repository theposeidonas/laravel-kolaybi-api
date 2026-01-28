<?php

namespace Theposeidonas\Kolaybi\Tests\Unit;

use Illuminate\Support\Facades\Http;
use Theposeidonas\Kolaybi\Exceptions\KolaybiApiException;
use Theposeidonas\Kolaybi\Exceptions\KolaybiValidationException;
use Theposeidonas\Kolaybi\Facades\Kolaybi;
use Theposeidonas\Kolaybi\Tests\TestCase;

class AssociateResourceTest extends TestCase
{
    public function test_list_success()
    {
        Http::fake([
            '*/kolaybi/v1/associates' => Http::response([
                'success' => true,
                'data' => [['id' => 1, 'name' => 'Baran']],
            ], 200),
        ]);

        $response = Kolaybi::associate()->list();

        $this->assertTrue($response->isSuccess());
        $this->assertCount(1, $response->getData());
    }

    public function test_list_fail()
    {
        Http::fake([
            '*/kolaybi/v1/associates' => Http::response([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401),
        ]);

        $this->expectException(KolaybiApiException::class);
        $this->expectExceptionCode(401);

        Kolaybi::associate()->list();
    }

    public function test_create_success()
    {
        Http::fake([
            '*/kolaybi/v1/associates' => Http::response([
                'success' => true,
                'data' => ['id' => 1],
            ], 201),
        ]);

        $response = Kolaybi::associate()->create([
            'name' => 'Baran',
            'surname' => 'Arda',
            'identity_no' => '12345678901',
        ]);

        $this->assertTrue($response->isSuccess());
    }

    public function test_create_fail()
    {
        $this->expectException(KolaybiValidationException::class);

        Kolaybi::associate()->create([
            'name' => 'B',
        ]);
    }

    public function test_address_create_success()
    {
        Http::fake([
            '*/kolaybi/v1/address/create' => Http::response([
                'success' => true,
                'data' => ['id' => 5],
            ], 200),
        ]);

        $response = Kolaybi::associate()->addressCreate([
            'associate_id' => 1,
            'city' => 'Istanbul',
            'district' => 'Kadikoy',
            'country' => 'Turkiye',
        ]);

        $this->assertTrue($response->isSuccess());
    }

    public function test_address_create_fail()
    {
        $this->expectException(KolaybiValidationException::class);

        Kolaybi::associate()->addressCreate([
            'associate_id' => 1,
        ]);
    }

    public function test_transactions_success()
    {
        Http::fake([
            '*/kolaybi/v1/associates/1/transactions' => Http::response([
                'success' => true,
                'data' => [],
            ], 200),
        ]);

        $response = Kolaybi::associate()->transactions(1);

        $this->assertTrue($response->isSuccess());
    }

    public function test_transactions_fail()
    {
        Http::fake([
            '*/kolaybi/v1/associates/999/transactions' => Http::response([
                'success' => false,
                'message' => 'Not Found',
            ], 404),
        ]);

        $this->expectException(KolaybiApiException::class);
        $this->expectExceptionCode(404);

        Kolaybi::associate()->transactions(999);
    }

    public function test_payment_success()
    {
        Http::fake([
            '*/kolaybi/v1/associates/1/transactions/payment' => Http::response([
                'success' => true,
                'data' => ['status' => 'success'],
            ], 200),
        ]);

        $response = Kolaybi::associate()->payment(1, [
            'amount' => 100,
            'currency' => 'TRY',
            'gateway_id' => 5,
        ]);

        $this->assertTrue($response->isSuccess());
    }

    public function test_payment_fail()
    {
        $this->expectException(KolaybiValidationException::class);

        Kolaybi::associate()->payment(1, [
            'amount' => 'invalid',
            'currency' => 'TRY',
            'gateway_id' => 5,
        ]);
    }

    public function test_proceed_success()
    {
        Http::fake([
            '*/kolaybi/v1/associates/1/transactions/proceed' => Http::response([
                'success' => true,
                'data' => ['status' => 'success'],
            ], 200),
        ]);

        $response = Kolaybi::associate()->proceed(1, [
            'amount' => 200,
            'currency' => 'USD',
            'gateway_id' => 10,
        ]);

        $this->assertTrue($response->isSuccess());
    }

    public function test_proceed_fail()
    {
        $this->expectException(KolaybiValidationException::class);

        Kolaybi::associate()->proceed(1, [
            'amount' => 200,
            'gateway_id' => 10,
        ]);
    }
}
