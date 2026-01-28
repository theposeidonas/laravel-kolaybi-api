<?php

namespace Theposeidonas\Kolaybi\Tests\Unit;

use Illuminate\Support\Facades\Http;
use Theposeidonas\Kolaybi\Exceptions\KolaybiApiException;
use Theposeidonas\Kolaybi\Exceptions\KolaybiValidationException;
use Theposeidonas\Kolaybi\Facades\Kolaybi;
use Theposeidonas\Kolaybi\Tests\TestCase;

class TagResourceTest extends TestCase
{
    public function test_list_success()
    {
        Http::fake([
            '*/kolaybi/v1/tags*' => Http::response([
                'success' => true,
                'data' => [['id' => 1, 'name' => 'Acil']]
            ], 200)
        ]);

        $response = Kolaybi::tag()->list(['group' => 'finance']);

        $this->assertTrue($response->isSuccess());
        $this->assertCount(1, $response->getData());
    }

    public function test_list_fail()
    {
        $this->expectException(KolaybiValidationException::class);

        Kolaybi::tag()->list(['group' => ['invalid_type']]);
    }

    public function test_find_success()
    {
        Http::fake([
            '*/kolaybi/v1/tags/1' => Http::response([
                'success' => true,
                'data' => ['id' => 1, 'name' => 'Acil']
            ], 200)
        ]);

        $response = Kolaybi::tag()->find(1);

        $this->assertTrue($response->isSuccess());
        $this->assertEquals(1, $response->getData()['id']);
    }

    public function test_find_fail()
    {
        Http::fake([
            '*/kolaybi/v1/tags/999' => Http::response([
                'success' => false,
                'message' => 'Tag not found'
            ], 404)
        ]);

        $this->expectException(KolaybiApiException::class);
        $this->expectExceptionCode(404);

        Kolaybi::tag()->find(999);
    }
}