<?php

namespace Theposeidonas\Kolaybi\Tests\Unit;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Theposeidonas\Kolaybi\Enums\ChequeStatus;
use Theposeidonas\Kolaybi\Enums\InvoiceType;
use Theposeidonas\Kolaybi\Exceptions\KolaybiValidationException;
use Theposeidonas\Kolaybi\Facades\Kolaybi;
use Theposeidonas\Kolaybi\Tests\TestCase;

class NewEndpointsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            '*' => Http::response(['success' => true, 'data' => []]),
        ]);
    }

    protected function assertRequest(string $method, string $path, ?callable $extra = null): void
    {
        Http::assertSent(fn (Request $request): bool => $request->method() === $method
            && parse_url($request->url(), PHP_URL_PATH) === $path
            && ($extra === null || $extra($request)));
    }

    public function test_associate_find(): void
    {
        Kolaybi::associate()->find(5);

        $this->assertRequest('GET', '/kolaybi/v1/associates/5');
    }

    public function test_associate_list_filters(): void
    {
        Kolaybi::associate()->list(['name' => 'Ali', 'active' => true, 'tag_ids' => [1, 2]]);

        $this->assertRequest('GET', '/kolaybi/v1/associates', fn (Request $r): bool => $r['name'] === 'Ali');
    }

    public function test_associate_transfer(): void
    {
        Kolaybi::associate()->transfer(5, [
            'destination_associate_id' => 6,
            'amount' => 100,
            'currency' => 'TRY',
            'cash_flow_direction' => -1,
        ]);

        $this->assertRequest('POST', '/kolaybi/v1/associates/5/transactions/transfer', fn (Request $r): bool => $r['destination_associate_id'] === 6);
    }

    public function test_associate_transfer_fail(): void
    {
        $this->expectException(KolaybiValidationException::class);

        Kolaybi::associate()->transfer(5, ['amount' => 100, 'currency' => 'TRY', 'cash_flow_direction' => 0]);
    }

    public function test_associate_payment_requires_gateway_type(): void
    {
        $this->expectException(KolaybiValidationException::class);

        Kolaybi::associate()->payment(5, ['amount' => 10, 'currency' => 'TRY', 'gateway_id' => 1]);
    }

    public function test_invoice_pay(): void
    {
        Kolaybi::invoice()->pay(['document_id' => 1, 'vault_id' => 2, 'amount' => 50]);

        $this->assertRequest('POST', '/kolaybi/v1/invoices/payment');
    }

    public function test_invoice_find_include_draft(): void
    {
        Kolaybi::invoice()->find(1, includeDraft: true);

        $this->assertRequest('GET', '/kolaybi/v1/invoices/1', fn (Request $r): bool => $r['include_draft'] == 1);
    }

    public function test_invoice_formalize_with_prefix(): void
    {
        Kolaybi::invoice()->formalize(1, 'ABC');

        $this->assertRequest('POST', '/kolaybi/v1/invoices/e-document/create', fn (Request $r): bool => $r['prefix'] === 'ABC');
    }

    public function test_invoice_view_e_document_options(): void
    {
        Kolaybi::invoice()->viewEDocument('uuid-1', ['output_type' => 'pdf', 'direction' => 'outbound']);

        $this->assertRequest('GET', '/kolaybi/v1/invoices/e-document/view', fn (Request $r): bool => $r['output_type'] === 'pdf');
    }

    public function test_invoice_general_expense_without_items(): void
    {
        Kolaybi::invoice()->create([
            'contact_id' => 1,
            'address_id' => 1,
            'order_date' => '2026-01-01',
            'currency' => 'TRY',
            'type' => InvoiceType::GENERAL_EXPENSE,
            'financial_action_type_id' => 3,
            'subtotal' => 100,
        ]);

        $this->assertRequest('POST', '/kolaybi/v1/invoices', fn (Request $r): bool => $r['type'] === 'general_expense');
    }

    public function test_invoice_waybill_requires_shipment_fields(): void
    {
        $this->expectException(KolaybiValidationException::class);

        Kolaybi::invoice()->create([
            'contact_id' => 1,
            'address_id' => 1,
            'order_date' => '2026-01-01',
            'currency' => 'TRY',
            'type' => 'sale_waybill',
            'items' => [['product_id' => 1, 'quantity' => '1', 'unit_price' => '1', 'vat_rate' => 20]],
        ]);
    }

    public function test_list_e_waybills(): void
    {
        Kolaybi::invoice()->listEWaybills(['company_id' => 1, 'direction' => 'inbound']);

        $this->assertRequest('GET', '/kolaybi/v1/e_document/waybills');
    }

    public function test_cheques(): void
    {
        Kolaybi::cheque()->list(['cheque_status' => ChequeStatus::PORTFOLIO, 'cash_flow_direction' => 1]);
        Kolaybi::cheque()->find(9);

        $this->assertRequest('GET', '/kolaybi/v1/cheques', fn (Request $r): bool => $r['cheque_status'] === 'portfolio');
        $this->assertRequest('GET', '/kolaybi/v1/cheques/9');
    }

    public function test_cheque_list_fail(): void
    {
        $this->expectException(KolaybiValidationException::class);

        Kolaybi::cheque()->list(['cheque_status' => 'unknown']);
    }

    public function test_bonds(): void
    {
        Kolaybi::bond()->list(['bond_status' => 'paid']);
        Kolaybi::bond()->find(4);

        $this->assertRequest('GET', '/kolaybi/v1/bonds');
        $this->assertRequest('GET', '/kolaybi/v1/bonds/4');
    }

    public function test_online_bank(): void
    {
        $bank = Kolaybi::onlineBank();

        $bank->banks();
        $bank->bankAccounts('b1');
        $bank->bankTransactions('b1', ['direction' => 'in', 'is_mapped' => false]);
        $bank->accounts();
        $bank->account('a1');
        $bank->accountTransactions('a1', ['start_date' => '2026-01-01', 'end_date' => '2026-02-01']);
        $bank->transaction('t1');
        $bank->map('t1', ['vault_id' => 1, 'invoices' => [['id' => 2, 'amount' => 10]]]);
        $bank->unmap('t1');

        $this->assertRequest('GET', '/kolaybi/v1/online_bank/banks');
        $this->assertRequest('GET', '/kolaybi/v1/online_bank/banks/b1/accounts');
        $this->assertRequest('GET', '/kolaybi/v1/online_bank/banks/b1/transactions', fn (Request $r): bool => $r['is_mapped'] == 0);
        $this->assertRequest('GET', '/kolaybi/v1/online_bank/accounts');
        $this->assertRequest('GET', '/kolaybi/v1/online_bank/accounts/a1');
        $this->assertRequest('GET', '/kolaybi/v1/online_bank/accounts/a1/transactions');
        $this->assertRequest('GET', '/kolaybi/v1/online_bank/transactions/t1');
        $this->assertRequest('POST', '/kolaybi/v1/online_bank/transactions/t1/map', fn (Request $r): bool => $r['vault_id'] === 1);
        $this->assertRequest('DELETE', '/kolaybi/v1/online_bank/transactions/t1/map');
    }

    public function test_online_bank_map_fail(): void
    {
        $this->expectException(KolaybiValidationException::class);

        Kolaybi::onlineBank()->map('t1', ['invoices' => [['id' => 1]]]);
    }

    public function test_financial_action_types(): void
    {
        Kolaybi::financialActionType()->list();

        $this->assertRequest('GET', '/kolaybi/v1/financial_action_types');
    }

    public function test_tag_sync(): void
    {
        Kolaybi::tag()->sync('CommercialDoc', 10, [1, 2]);

        $this->assertRequest('PUT', '/kolaybi/v1/tags/CommercialDoc/10', fn (Request $r): bool => $r['relations'] === ['tags' => [['id' => 1], ['id' => 2]]]);
    }
}
