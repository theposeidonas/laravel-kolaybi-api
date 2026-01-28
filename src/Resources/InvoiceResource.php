<?php

namespace Theposeidonas\Kolaybi\Resources;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Validation\Rule;
use Theposeidonas\Kolaybi\Enums\Currency;
use Theposeidonas\Kolaybi\Enums\InvoiceType;
use Theposeidonas\Kolaybi\Enums\VATRate;
use Theposeidonas\Kolaybi\Exceptions\KolaybiApiException;
use Theposeidonas\Kolaybi\Exceptions\KolaybiValidationException;
use Theposeidonas\Kolaybi\Responses\KolaybiResponse;

class InvoiceResource extends BaseResource
{
    /**
     * @throws KolaybiApiException
     * @throws KolaybiValidationException
     * @throws ConnectionException
     */
    public function list(array $query = []): KolaybiResponse
    {
        $validated = $this->validate($query, [
            'type' => ['nullable', Rule::in(InvoiceType::values())],
            'associate_id' => 'nullable|integer',
            'has_products' => 'nullable|boolean',
        ]);

        return $this->client->get('/kolaybi/v1/invoices', $validated);
    }

    /**
     * @throws KolaybiApiException
     * @throws KolaybiValidationException
     * @throws ConnectionException
     */
    public function create(array $data): KolaybiResponse
    {
        $validated = $this->validate($data, [
            'contact_id' => 'required|integer',
            'address_id' => 'required|integer',
            'order_date' => 'required|date_format:Y-m-d',
            'currency' => ['required', Rule::in(Currency::values())],
            'serial_no' => 'nullable|string',
            'due_date' => 'nullable|date_format:Y-m-d',
            'description' => 'nullable|string',
            'receiver_email' => 'nullable|email',
            'type' => ['nullable', Rule::in(InvoiceType::values())],
            'tracking_currency' => ['nullable', Rule::in(Currency::values())],
            'tags' => 'nullable|array',
            'subtotal_discount_amount' => 'nullable|numeric',
            'subtotal_correction_amount' => 'nullable|numeric',
            'exchange_rate' => 'nullable|string',
            'cross_currency_rate' => 'nullable|string',
            'document_scenario' => 'nullable|string|in:TICARIFATURA,TEMELFATURA,ILAC_TIBBICIHAZ',
            'document_type' => 'nullable|string',
            'vat_exemption_reason_code' => 'required_if:document_type,ISTISNA|nullable|string',
            'shipment_include' => 'nullable|boolean',

            'order_reference' => 'nullable|array',
            'order_reference.serial_no' => 'nullable|string',
            'order_reference.issue_date' => 'nullable|date',

            'return_invoice_references' => 'nullable|array',
            'return_invoice_references.*.serial_no' => 'nullable|string',
            'return_invoice_references.*.issue_date' => 'nullable|date',

            'special_tax_base' => 'nullable|array',
            'special_tax_base.reason_code' => 'nullable|string',
            'special_tax_base.total' => 'nullable|string',
            'special_tax_base.percentage' => 'nullable|string',
            'special_tax_base.vat' => 'nullable|string',

            'internet_sale' => 'nullable|array',
            'internet_sale.url' => 'nullable|url',
            'internet_sale.payment_type' => 'nullable|in:credit-card,bank-transfer,pay-at-door,payment-platform',
            'internet_sale.payment_platform' => 'nullable|string',
            'internet_sale.payment_date' => 'nullable|date',

            'internet_sale_shipment' => 'nullable|array',
            'internet_sale_shipment.carrier_company' => 'nullable|string',
            'internet_sale_shipment.carrier_person' => 'nullable|string',
            'internet_sale_shipment.carrier_company_tax_number' => 'nullable|string',
            'internet_sale_shipment.carrier_date' => 'nullable|date',

            'handling_and_shipping_information' => 'nullable|array',
            'handling_and_shipping_information.delivery_condition' => 'nullable|in:FAS,FOB,DDU,DDP,EXW,DEQ,CFR',
            'handling_and_shipping_information.shipping_method' => 'nullable|integer|between:1,7',
            'handling_and_shipping_information.shipping_method_detail' => 'nullable|string',
            'handling_and_shipping_information.freight' => 'nullable|string',
            'handling_and_shipping_information.insurance_amount' => 'nullable|string',

            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer',
            'items.*.quantity' => 'required|string',
            'items.*.unit_price' => 'required|string',
            'items.*.vat_rate' => ['required', Rule::in(VATRate::values())],
            'items.*.description' => 'nullable|string',
            'items.*.discount_amount' => 'nullable|numeric',
            'items.*.gtip_no' => 'nullable|string|size:12',

            'items.*.medicine_medicaldevice_type' => 'required_if:document_scenario,ILAC_TIBBICIHAZ|nullable|in:medicine,medical_device',
            'items.*.identifications' => 'required_if:items.*.medicine_medicaldevice_type,medicine,medical_device|nullable|array',
            'items.*.identifications.*.bn' => 'required_if:items.*.medicine_medicaldevice_type,medicine|nullable|string',
            'items.*.identifications.*.gtin' => 'required_if:items.*.medicine_medicaldevice_type,medicine|nullable|string',
            'items.*.identifications.*.sn' => 'required_if:items.*.medicine_medicaldevice_type,medicine|nullable|string',
            'items.*.identifications.*.xd' => 'required_if:items.*.medicine_medicaldevice_type,medicine|nullable|date_format:Y-m-d',
            'items.*.identifications.*.uno' => 'required_if:items.*.medicine_medicaldevice_type,medical_device|nullable|string',
            'items.*.identifications.*.lno' => 'required_if:items.*.medicine_medicaldevice_type,medical_device|nullable|string',
            'items.*.identifications.*.sno' => 'nullable|string',
            'items.*.identifications.*.urt' => 'required_if:items.*.medicine_medicaldevice_type,medical_device|nullable|date_format:Y-m-d',
        ]);

        return $this->client->post('/kolaybi/v1/invoices', $validated);
    }

    /**
     * @throws KolaybiApiException
     * @throws ConnectionException
     */
    public function find(int $documentId): KolaybiResponse
    {
        return $this->client->get("/kolaybi/v1/invoices/{$documentId}");
    }

    /**
     * @throws KolaybiApiException
     * @throws KolaybiValidationException
     * @throws ConnectionException
     */
    public function formalize(int $documentId): KolaybiResponse
    {
        $validated = $this->validate(['document_id' => $documentId], [
            'document_id' => 'required|integer',
        ]);

        return $this->client->post('/kolaybi/v1/invoices/e-document/create', $validated);
    }

    /**
     * @throws KolaybiApiException
     * @throws KolaybiValidationException
     * @throws ConnectionException
     */
    public function collect(array $data): KolaybiResponse
    {
        $validated = $this->validate($data, [
            'document_id' => 'required|integer',
            'vault_id' => 'required|integer',
            'amount' => 'nullable|numeric',
        ]);

        return $this->client->post('/kolaybi/v1/invoices/proceed', $validated);
    }

    /**
     * @throws KolaybiApiException
     * @throws ConnectionException
     */
    public function delete(int $documentId): KolaybiResponse
    {
        return $this->client->delete("/kolaybi/v1/invoices/{$documentId}");
    }

    /**
     * @throws KolaybiApiException
     * @throws ConnectionException
     */
    public function deleteCollection(int $documentId): KolaybiResponse
    {
        return $this->client->delete("/kolaybi/v1/invoices/proceed/{$documentId}");
    }

    /**
     * @throws KolaybiApiException
     * @throws KolaybiValidationException
     * @throws ConnectionException
     */
    public function cancelEDocument(int $documentId): KolaybiResponse
    {
        $validated = $this->validate(['document_id' => $documentId], [
            'document_id' => 'required|integer',
        ]);

        return $this->client->post('/kolaybi/v1/invoices/e-document/cancel', $validated);
    }

    /**
     * @throws KolaybiApiException
     * @throws KolaybiValidationException
     * @throws ConnectionException
     */
    public function viewEDocument(string $uuid): KolaybiResponse
    {
        $validated = $this->validate(['uuid' => $uuid], [
            'uuid' => 'required|string',
        ]);

        return $this->client->get('/kolaybi/v1/invoices/e-document/view', $validated);
    }

    /**
     * @throws KolaybiApiException
     * @throws KolaybiValidationException
     * @throws ConnectionException
     */
    public function resendEDocument(int $documentId): KolaybiResponse
    {
        $validated = $this->validate(['document_id' => $documentId], [
            'document_id' => 'required|integer',
        ]);

        return $this->client->post("/kolaybi/v1/invoices/resend/{$documentId}");
    }

    /**
     * @throws KolaybiApiException
     * @throws KolaybiValidationException
     * @throws ConnectionException
     */
    public function listEInvoices(array $query): KolaybiResponse
    {
        $validated = $this->validate($query, [
            'company_id' => 'required|integer',
            'direction' => 'required|string',
            'document_id' => 'nullable|integer',
            'min_issue_date' => 'nullable|date_format:Y-m-d',
            'max_issue_date' => 'nullable|date_format:Y-m-d',
            'min_total_amount' => 'nullable|numeric',
            'max_total_amount' => 'nullable|numeric',
            'party_name' => 'nullable|string',
        ]);

        return $this->client->get('/kolaybi/v1/e_document/invoices', $validated);
    }
}
