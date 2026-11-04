<?php

namespace Theposeidonas\Kolaybi\Tests\Feature;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Theposeidonas\Kolaybi\Exceptions\KolaybiApiException;
use Theposeidonas\Kolaybi\Exceptions\KolaybiValidationException;
use Theposeidonas\Kolaybi\Facades\Kolaybi;
use Theposeidonas\Kolaybi\Tests\TestCase;

class AuthenticationTest extends TestCase
{
    /**
     * @throws KolaybiApiException
     * @throws KolaybiValidationException
     * @throws ConnectionException
     */
    public function test_it_can_list_tags_with_mock_data()
    {
        Http::fake([
            '*/access_token' => Http::response([
                'success' => true,
                'data' => 'mock-token',
            ], 200),

            '*/kolaybi/v1/tags' => Http::response([
                'success' => true,
                'data' => [
                    ['id' => 1, 'name' => 'Acil'],
                    ['id' => 2, 'name' => 'Önemli'],
                ],
            ], 200),
        ]);

        $response = Kolaybi::tag()->list();

        $this->assertTrue($response->isSuccess());
        $this->assertEquals(200, $response->getStatus());

        $data = $response->getData();

        $this->assertIsArray($data);
        $this->assertCount(2, $data);
        $this->assertEquals('Acil', $data[0]['name']);
    }
}
