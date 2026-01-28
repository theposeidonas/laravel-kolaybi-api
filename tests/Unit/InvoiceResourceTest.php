<?php

namespace Theposeidonas\Kolaybi\Tests\Unit;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Theposeidonas\Kolaybi\Exceptions\KolaybiApiException;
use Theposeidonas\Kolaybi\Exceptions\KolaybiValidationException;
use Theposeidonas\Kolaybi\Facades\Kolaybi;
use Theposeidonas\Kolaybi\Tests\TestCase;

class InvoiceResourceTest extends TestCase
{
    /**
     * @throws KolaybiApiException
     * @throws KolaybiValidationException
     * @throws ConnectionException
     */
    public function test_list_success()
    {
        Http::fake([
            '*/kolaybi/v1/invoices*' => Http::response([
                'success' => true,
                'data' => [['id' => 1, 'serial_no' => 'INV2026001']]
            ], 200)
        ]);

        $validType = \Theposeidonas\Kolaybi\Enums\InvoiceType::values()[0];

        $response = \Theposeidonas\Kolaybi\Facades\Kolaybi::invoice()->list(['type' => $validType]);

        $this->assertTrue($response->isSuccess());
        $this->assertCount(1, $response->getData());
    }

    public function test_list_fail()
    {
        $this->expectException(KolaybiValidationException::class);

        Kolaybi::invoice()->list(['type' => 'invalid_type']);
    }

    public function test_create_success()
    {
        Http::fake([
            '*/kolaybi/v1/invoices' => Http::response([
                'success' => true,
                'data' => ['id' => 100]
            ], 201)
        ]);

        $response = Kolaybi::invoice()->create([
            'contact_id' => 1,
            'address_id' => 1,
            'order_date' => '2026-01-28',
            'currency' => 'TRY',
            'items' => [
                [
                    'product_id' => 1,
                    'quantity' => '1',
                    'unit_price' => '100',
                    'vat_rate' => 20
                ]
            ]
        ]);

        $this->assertTrue($response->isSuccess());
    }

    public function test_create_fail()
    {
        $this->expectException(KolaybiValidationException::class);

        Kolaybi::invoice()->create([
            'contact_id' => 1
        ]);
    }

    public function test_find_success()
    {
        Http::fake([
            '*/kolaybi/v1/invoices/100' => Http::response([
                'success' => true,
                'data' => ['id' => 100]
            ], 200)
        ]);

        $response = Kolaybi::invoice()->find(100);

        $this->assertTrue($response->isSuccess());
        $this->assertEquals(100, $response->getData()['id']);
    }

    public function test_find_fail()
    {
        Http::fake([
            '*/kolaybi/v1/invoices/999' => Http::response([
                'success' => false,
                'message' => 'Not Found'
            ], 404)
        ]);

        $this->expectException(KolaybiApiException::class);
        $this->expectExceptionCode(404);

        Kolaybi::invoice()->find(999);
    }

    public function test_formalize_success()
    {
        Http::fake([
            '*/kolaybi/v1/invoices/e-document/create' => Http::response([
                'success' => true,
                'data' => ['status' => 'queued']
            ], 200)
        ]);

        $response = Kolaybi::invoice()->formalize(100);

        $this->assertTrue($response->isSuccess());
    }

    public function test_formalize_fail()
    {
        Http::fake([
            '*/kolaybi/v1/invoices/e-document/create' => Http::response([
                'success' => false,
                'message' => 'Error'
            ], 400)
        ]);

        $this->expectException(KolaybiApiException::class);
        $this->expectExceptionCode(400);

        Kolaybi::invoice()->formalize(100);
    }

    public function test_collect_success()
    {
        Http::fake([
            '*/kolaybi/v1/invoices/proceed' => Http::response([
                'success' => true,
                'data' => ['id' => 1]
            ], 200)
        ]);

        $response = Kolaybi::invoice()->collect([
            'document_id' => 100,
            'vault_id' => 1,
            'amount' => 500
        ]);

        $this->assertTrue($response->isSuccess());
    }

    public function test_collect_fail()
    {
        $this->expectException(KolaybiValidationException::class);

        Kolaybi::invoice()->collect([
            'document_id' => 100
        ]);
    }

    public function test_delete_success()
    {
        Http::fake([
            '*/kolaybi/v1/invoices/100' => Http::response([
                'success' => true
            ], 200)
        ]);

        $response = Kolaybi::invoice()->delete(100);

        $this->assertTrue($response->isSuccess());
    }

    public function test_delete_fail()
    {
        Http::fake([
            '*/kolaybi/v1/invoices/100' => Http::response([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401)
        ]);

        $this->expectException(KolaybiApiException::class);
        $this->expectExceptionCode(401);

        Kolaybi::invoice()->delete(100);
    }

    public function test_delete_collection_success()
    {
        Http::fake([
            '*/kolaybi/v1/invoices/proceed/1' => Http::response([
                'success' => true
            ], 200)
        ]);

        $response = Kolaybi::invoice()->deleteCollection(1);

        $this->assertTrue($response->isSuccess());
    }

    public function test_delete_collection_fail()
    {
        Http::fake([
            '*/kolaybi/v1/invoices/proceed/1' => Http::response([
                'success' => false,
                'message' => 'Error'
            ], 500)
        ]);

        $this->expectException(KolaybiApiException::class);
        $this->expectExceptionCode(500);

        Kolaybi::invoice()->deleteCollection(1);
    }

    public function test_cancel_e_document_success()
    {
        Http::fake([
            '*/kolaybi/v1/invoices/e-document/cancel' => Http::response([
                'success' => true
            ], 200)
        ]);

        $response = Kolaybi::invoice()->cancelEDocument(100);

        $this->assertTrue($response->isSuccess());
    }

    public function test_cancel_e_document_fail()
    {
        Http::fake([
            '*/kolaybi/v1/invoices/e-document/cancel' => Http::response([
                'success' => false,
                'message' => 'Cannot cancel'
            ], 422)
        ]);

        $this->expectException(KolaybiApiException::class);

        Kolaybi::invoice()->cancelEDocument(100);
    }

    public function test_view_e_document_success()
    {
        Http::fake([
            '*/kolaybi/v1/invoices/e-document/view*' => Http::response([
                'success' => true,
                'data' => 'html_content'
            ], 200)
        ]);

        $response = Kolaybi::invoice()->viewEDocument('uuid-string');

        $this->assertTrue($response->isSuccess());
    }

    public function test_view_e_document_fail()
    {
        $this->expectException(KolaybiValidationException::class);

        Kolaybi::invoice()->viewEDocument('');
    }

    public function test_resend_e_document_success()
    {
        Http::fake([
            '*/kolaybi/v1/invoices/resend/100' => Http::response([
                'success' => true
            ], 200)
        ]);

        $response = Kolaybi::invoice()->resendEDocument(100);

        $this->assertTrue($response->isSuccess());
    }

    public function test_resend_e_document_fail()
    {
        Http::fake([
            '*/kolaybi/v1/invoices/resend/100' => Http::response([
                'success' => false,
                'message' => 'Limit reached'
            ], 429)
        ]);

        $this->expectException(KolaybiApiException::class);
        $this->expectExceptionCode(429);

        Kolaybi::invoice()->resendEDocument(100);
    }

    public function test_list_e_invoices_success()
    {
        Http::fake([
            '*/kolaybi/v1/e_document/invoices*' => Http::response([
                'success' => true,
                'data' => []
            ], 200)
        ]);

        $response = Kolaybi::invoice()->listEInvoices([
            'company_id' => 5,
            'direction' => 'inbound'
        ]);

        $this->assertTrue($response->isSuccess());
    }

    public function test_list_e_invoices_fail()
    {
        $this->expectException(KolaybiValidationException::class);

        Kolaybi::invoice()->listEInvoices([]);
    }
}