<?php

namespace Theposeidonas\Kolaybi\Tests\Unit;

use Illuminate\Support\Facades\Http;
use Theposeidonas\Kolaybi\Exceptions\KolaybiApiException;
use Theposeidonas\Kolaybi\Facades\Kolaybi;
use Theposeidonas\Kolaybi\Tests\TestCase;

class UserResourceTest extends TestCase
{
    public function test_list_success()
    {
        Http::fake([
            '*/kolaybi/v1/users' => Http::response([
                'success' => true,
                'data' => [
                    ['id' => 1, 'email' => 'baran@baran.com'],
                    ['id' => 2, 'email' => 'arda@baran.com']
                ]
            ], 200)
        ]);

        $response = Kolaybi::user()->list();

        $this->assertTrue($response->isSuccess());
        $this->assertCount(2, $response->getData());
        $this->assertEquals('baran@baran.com', $response->getData()[0]['email']);
    }

    public function test_list_fail()
    {
        Http::fake([
            '*/kolaybi/v1/users' => Http::response([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 401)
        ]);

        $this->expectException(KolaybiApiException::class);
        $this->expectExceptionCode(401);

        Kolaybi::user()->list();
    }
}