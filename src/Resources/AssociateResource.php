<?php

namespace Theposeidonas\Kolaybi\Resources;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Validation\Rule;
use Theposeidonas\Kolaybi\Enums\AddressType;
use Theposeidonas\Kolaybi\Enums\AssociateType;
use Theposeidonas\Kolaybi\Enums\CashFlowDirection;
use Theposeidonas\Kolaybi\Enums\Currency;
use Theposeidonas\Kolaybi\Enums\GatewayType;
use Theposeidonas\Kolaybi\Exceptions\KolaybiApiException;
use Theposeidonas\Kolaybi\Exceptions\KolaybiValidationException;
use Theposeidonas\Kolaybi\Responses\KolaybiResponse;

class AssociateResource extends BaseResource
{
    /**
     * @param  array<string, mixed>  $query
     *
     * @throws KolaybiApiException|KolaybiValidationException|ConnectionException
     */
    public function list(array $query = []): KolaybiResponse
    {
        $validated = $this->validate($query, [
            'code' => 'nullable|string',
            'name' => 'nullable|string',
            'surname' => 'nullable|string',
            'active' => 'nullable|boolean',
            'identity_no' => 'nullable|string',
            'email' => 'nullable|string',
            'group' => 'nullable|string',
            'source' => 'nullable|string',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'integer',
        ]);

        return $this->client->get('/kolaybi/v1/associates', $validated);
    }

    /**
     * @throws KolaybiApiException|ConnectionException
     */
    public function find(int $associateId): KolaybiResponse
    {
        return $this->client->get("/kolaybi/v1/associates/{$associateId}");
    }

    /**
     * @throws KolaybiApiException
     * @throws KolaybiValidationException
     * @throws ConnectionException
     */
    public function create(array $data): KolaybiResponse
    {
        $validated = $this->validate($data, [
            'name' => 'required|string|min:2',
            'surname' => 'required|string|min:2',
            'identity_no' => 'required|string|between:10,11',
            'is_corporate' => 'nullable|boolean',
            'associate_type' => ['nullable', Rule::in(AssociateType::values())],
            'alias' => 'nullable|string',
            'tax_office' => 'nullable|string',
            'website' => 'nullable|url',
            'code' => 'nullable|string',
            'source' => 'nullable|string',
            'tags' => 'nullable|array',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',

            'addresses' => 'nullable|array',
            'addresses.*.address' => 'nullable|string',
            'addresses.*.city' => 'nullable|string',
            'addresses.*.district' => 'nullable|string',
            'addresses.*.country' => 'nullable|string',
            'addresses.*.address_type' => ['nullable', Rule::in(AddressType::values())],
            'addresses.*.is_abroad' => 'nullable|boolean',
            'addresses.*.building_name' => 'nullable|string',
            'addresses.*.number' => 'nullable|numeric',
            'addresses.*.postal_code' => 'nullable|string',
            'addresses.*.street' => 'nullable|string',
        ]);

        return $this->client->post('/kolaybi/v1/associates', $validated);
    }

    /**
     * @throws KolaybiApiException
     * @throws KolaybiValidationException|ConnectionException
     */
    public function addressCreate(array $data): KolaybiResponse
    {
        $validated = $this->validate($data, [
            'associate_id' => 'required|integer',
            'address' => 'nullable|string',
            'city' => 'required|string',
            'district' => 'required|string',
            'country' => 'required|string',
            'address_type' => ['nullable', Rule::in(AddressType::values())],
            'is_abroad' => 'nullable|boolean',
            'street' => 'required_if:is_abroad,true|nullable|string',
            'building_name' => 'required_if:is_abroad,true|nullable|string',
            'number' => 'required_if:is_abroad,true|nullable|numeric',
            'postal_code' => 'required_if:is_abroad,true|nullable|string',
            'address_location' => 'nullable|string',
        ]);

        return $this->client->post('/kolaybi/v1/address/create', $validated);
    }

    /**
     * @throws KolaybiApiException|ConnectionException
     */
    public function transactions(int $associateId): KolaybiResponse
    {
        return $this->client->get("/kolaybi/v1/associates/{$associateId}/transactions");
    }

    /**
     * @throws KolaybiApiException
     * @throws KolaybiValidationException|ConnectionException
     */
    public function payment(int $id, array $data): KolaybiResponse
    {
        $validated = $this->validate($data, $this->transactionRules());

        return $this->client->post("/kolaybi/v1/associates/{$id}/transactions/payment", $validated);
    }

    /**
     * @throws KolaybiApiException
     * @throws KolaybiValidationException|ConnectionException
     */
    public function proceed(int $id, array $data): KolaybiResponse
    {
        $validated = $this->validate($data, $this->transactionRules());

        return $this->client->post("/kolaybi/v1/associates/{$id}/transactions/proceed", $validated);
    }

    /**
     * @param  array<string, mixed>  $data
     *
     * @throws KolaybiApiException|KolaybiValidationException|ConnectionException
     */
    public function transfer(int $id, array $data): KolaybiResponse
    {
        $validated = $this->validate($data, [
            'destination_associate_id' => 'required|integer',
            'amount' => 'required|numeric',
            'currency' => ['required', Rule::in(Currency::values())],
            'cash_flow_direction' => ['required', 'integer', Rule::in([CashFlowDirection::IN->value, CashFlowDirection::OUT->value])],
            'exchange_rate' => 'nullable|numeric',
            'destination_currency' => ['nullable', Rule::in(Currency::values())],
            'destination_exchange_rate' => 'nullable|numeric',
            'issue_date' => 'nullable|string',
            'description' => 'nullable|string',
            'project_ids' => 'nullable|array',
            'project_ids.*' => 'integer',

            'expenses' => 'nullable|array',
            'expenses.*.source_type' => ['nullable', 'string', Rule::in(GatewayType::values())],
            'expenses.*.source_id' => 'nullable|integer',
            'expenses.*.amount' => 'nullable|numeric',
            'expenses.*.currency' => ['nullable', Rule::in(Currency::values())],
            'expenses.*.exchange_rate' => 'nullable|numeric',

            'transaction_currency_rates' => 'nullable|array',
            'transaction_currency_rates.*.quote_currency' => ['nullable', Rule::in(Currency::values())],
            'transaction_currency_rates.*.rate' => 'nullable|numeric',
        ]);

        return $this->client->post("/kolaybi/v1/associates/{$id}/transactions/transfer", $validated);
    }

    /**
     * @return array<string, mixed>
     */
    protected function transactionRules(): array
    {
        return [
            'amount' => 'required|numeric',
            'currency' => ['required', Rule::in(Currency::values())],
            'gateway_id' => 'required|integer',
            'gateway_type' => ['required', 'string', Rule::in(GatewayType::values())],
            'issue_date' => 'nullable|string',
            'exchange_rate' => 'nullable|numeric',
            'project_ids' => 'nullable|array',
            'project_ids.*' => 'integer',
            'description' => 'nullable|string',

            'expenses' => 'nullable|array',
            'expenses.*.source_type' => ['nullable', 'string', Rule::in(GatewayType::values())],
            'expenses.*.amount' => 'nullable|numeric',
            'expenses.*.currency' => ['nullable', Rule::in(Currency::values())],
            'expenses.*.source_id' => 'nullable|integer',

            'transaction_currency_rates' => 'nullable|array',
            'transaction_currency_rates.*.quote_currency' => ['nullable', Rule::in(Currency::values())],
            'transaction_currency_rates.*.rate' => 'nullable|numeric',
            'transaction_currency_rates.*.total' => 'nullable|numeric',
        ];
    }
}
