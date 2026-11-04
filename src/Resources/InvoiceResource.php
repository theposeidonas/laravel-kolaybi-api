<?php

namespace Theposeidonas\Kolaybi\Resources;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Validation\Rule;
use Theposeidonas\Kolaybi\Enums\Currency;
use Theposeidonas\Kolaybi\Enums\DocumentScenario;
use Theposeidonas\Kolaybi\Enums\DocumentType;
use Theposeidonas\Kolaybi\Enums\EDocumentDirection;
use Theposeidonas\Kolaybi\Enums\InvoiceType;
use Theposeidonas\Kolaybi\Enums\OutputType;
use Theposeidonas\Kolaybi\Enums\StoppageName;
use Theposeidonas\Kolaybi\Enums\VATRate;
use Theposeidonas\Kolaybi\Enums\WithholdingType;
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
            'min_issue_date' => 'nullable|date_format:Y-m-d',
            'max_issue_date' => 'nullable|date_format:Y-m-d|after_or_equal:min_issue_date',
            'description' => 'nullable|string',
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
            'order_date' => 'required|date',
            'currency' => ['required', Rule::in(Currency::values())],
            'serial_no' => 'nullable|string',
            'due_date' => 'nullable|date',
            'description' => 'nullable|string',
            'receiver_email' => 'nullable|email',
            'type' => ['nullable', Rule::in(InvoiceType::values())],
            'tracking_currency' => ['nullable', Rule::in(Currency::values())],
            'project_id' => 'nullable|integer',
            'tags' => 'nullable|array',
            'subtotal_discount_amount' => 'nullable|numeric',
            'subtotal_correction_amount' => 'nullable|numeric',
            'exchange_rate' => 'nullable|string',
            'cross_currency_rate' => 'nullable|string',
            'document_scenario' => ['nullable', 'string', Rule::in(DocumentScenario::values())],
            'document_type' => ['nullable', 'string', Rule::in(DocumentType::values())],
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

            'shipment_date' => 'required_if:type,sale_waybill,purchase_waybill|nullable|date',
            'firm_information' => 'required_if:type,sale_waybill,purchase_waybill|nullable|integer',
            'firm_information_address' => 'required_if:type,sale_waybill,purchase_waybill|nullable|integer',
            'driver_information' => 'nullable|array',
            'driver_information.*.first_name' => 'nullable|string',
            'driver_information.*.last_name' => 'nullable|string',
            'driver_information.*.title' => 'nullable|string',
            'driver_information.*.identity_no' => 'nullable|string',
            'vehicle' => 'nullable|array',
            'vehicle.plate_number' => 'nullable|string',
            'vehicle.trailers' => 'nullable|array',
            'vehicle.trailers.*.plate_number' => 'nullable|string',

            'financial_action_type_id' => 'required_if:type,general_expense|nullable|integer',
            'subtotal' => 'required_if:type,general_expense|nullable|numeric',
            'vat_type' => 'nullable|string|in:percentage,numeric',
            'vat_value' => 'nullable|numeric',
            'total_vat' => 'nullable|numeric',
            'total_otv' => 'nullable|numeric',
            'total_oiv' => 'nullable|numeric',
            'total_bsmv' => 'nullable|numeric',

            'items' => 'required_unless:type,general_expense|nullable|array|min:1',
            'items.*.product_id' => 'required|integer',
            'items.*.quantity' => 'required|string',
            'items.*.unit_price' => 'required|string',
            'items.*.vat_rate' => ['required', Rule::in(VATRate::values())],
            'items.*.description' => 'nullable|string',
            'items.*.discount_amount' => 'nullable|numeric',
            'items.*.gtip_no' => 'nullable|digits:12',
            'items.*.withholding_code' => 'nullable|string',
            'items.*.withholding_value' => 'nullable|numeric',
            'items.*.withholding_type' => ['nullable', Rule::in(WithholdingType::values())],
            'items.*.stoppage_value' => 'nullable|numeric',
            'items.*.stoppage_name' => ['nullable', Rule::in(StoppageName::values())],

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
    public function find(int $documentId, bool $includeDraft = false): KolaybiResponse
    {
        return $this->client->get("/kolaybi/v1/invoices/{$documentId}", $includeDraft ? ['include_draft' => 1] : []);
    }

    /**
     * @throws KolaybiApiException
     * @throws KolaybiValidationException
     * @throws ConnectionException
     */
    public function formalize(int $documentId, ?string $prefix = null): KolaybiResponse
    {
        $validated = $this->validate(array_filter(['document_id' => $documentId, 'prefix' => $prefix], fn (mixed $value): bool => $value !== null), [
            'document_id' => 'required|integer',
            'prefix' => 'nullable|string',
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
            'issue_date' => 'nullable|string',
            'serial_no' => 'nullable|string',
            'description' => 'nullable|string',
            'project_id' => 'nullable|integer',
        ]);

        return $this->client->post('/kolaybi/v1/invoices/proceed', $validated);
    }

    /**
     * @param  array<string, mixed>  $data
     *
     * @throws KolaybiApiException|KolaybiValidationException|ConnectionException
     */
    public function pay(array $data): KolaybiResponse
    {
        $validated = $this->validate($data, [
            'document_id' => 'required|integer',
            'vault_id' => 'required|integer',
            'amount' => 'nullable|numeric',
            'issue_date' => 'nullable|string',
            'exchange_rate' => 'nullable|numeric',
            'serial_no' => 'nullable|string',
            'description' => 'nullable|string',
            'project_id' => 'nullable|integer',
        ]);

        return $this->client->post('/kolaybi/v1/invoices/payment', $validated);
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
    public function viewEDocument(string $uuid, array $query = []): KolaybiResponse
    {
        $validated = $this->validate(['uuid' => $uuid, ...$query], [
            'uuid' => 'required|string',
            'direction' => ['nullable', Rule::in(EDocumentDirection::values())],
            'output_type' => ['nullable', Rule::in(OutputType::values())],
        ]);

        return $this->client->get('/kolaybi/v1/invoices/e-document/view', $validated);
    }

    /**
     * @throws KolaybiApiException
     * @throws ConnectionException
     */
    public function resendEDocument(int $documentId): KolaybiResponse
    {
        return $this->client->post("/kolaybi/v1/invoices/resend/{$documentId}");
    }

    /**
     * @throws KolaybiApiException
     * @throws KolaybiValidationException
     * @throws ConnectionException
     */
    public function listEInvoices(array $query): KolaybiResponse
    {
        return $this->client->get('/kolaybi/v1/e_document/invoices', $this->validate($query, $this->eDocumentListRules()));
    }

    /**
     * @param  array<string, mixed>  $query
     *
     * @throws KolaybiApiException|KolaybiValidationException|ConnectionException
     */
    public function listEWaybills(array $query): KolaybiResponse
    {
        return $this->client->get('/kolaybi/v1/e_document/waybills', $this->validate($query, $this->eDocumentListRules()));
    }

    /**
     * @return array<string, mixed>
     */
    protected function eDocumentListRules(): array
    {
        return [
            'company_id' => 'required|integer',
            'direction' => ['required', 'string', Rule::in(EDocumentDirection::values())],
            'document_id' => 'nullable|integer',
            'document_type' => ['nullable', Rule::in(DocumentType::values())],
            'document_scenario' => ['nullable', Rule::in(DocumentScenario::values())],
            'min_issue_date' => 'nullable|date_format:Y-m-d',
            'max_issue_date' => 'nullable|date_format:Y-m-d|after_or_equal:min_issue_date',
            'min_total_amount' => 'nullable|numeric',
            'max_total_amount' => 'nullable|numeric|gte:min_total_amount',
            'party_name' => 'nullable|string',
        ];
    }
}
