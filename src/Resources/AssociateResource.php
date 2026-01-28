<?php

namespace Theposeidonas\Kolaybi\Resources;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Validation\Rule;
use Theposeidonas\Kolaybi\Enums\AddressType;
use Theposeidonas\Kolaybi\Enums\AssociateType;
use Theposeidonas\Kolaybi\Enums\Currency;
use Theposeidonas\Kolaybi\Exceptions\KolaybiApiException;
use Theposeidonas\Kolaybi\Exceptions\KolaybiValidationException;
use Theposeidonas\Kolaybi\Responses\KolaybiResponse;

class AssociateResource extends BaseResource
{
    /**
     * @throws KolaybiApiException|ConnectionException
     */
    public function list(): KolaybiResponse
    {
        return $this->client->get('/kolaybi/v1/associates');
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
            'tax_office' => 'nullable|string',
            'website' => 'nullable|url',
            'code' => 'nullable|string',
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
        $validated = $this->validate($data, [
            'amount' => 'required|numeric',
            'currency' => ['required', Rule::in(Currency::values())],
            'gateway_id' => 'required|integer',
            'gateway_type' => 'nullable|string|in:Vault,Associate',
            'exchange_rate' => 'nullable|numeric',
            'project_ids' => 'nullable|array',
            'project_ids.*' => 'integer',
            'description' => 'nullable|string',

            'expenses' => 'nullable|array',
            'expenses.*.source_type' => 'nullable|string|in:Associate,Vault',
            'expenses.*.amount' => 'nullable|numeric',
            'expenses.*.currency' => ['nullable', Rule::in(Currency::values())],
            'expenses.*.source_id' => 'nullable|integer',

            'transaction_currency_rates' => 'nullable|array',
            'transaction_currency_rates.*.quote_currency' => ['nullable', Rule::in(Currency::values())],
            'transaction_currency_rates.*.rate' => 'nullable|numeric',
            'transaction_currency_rates.*.total' => 'nullable|numeric',
        ]);

        return $this->client->post("/kolaybi/v1/associates/{$id}/transactions/payment", $validated);
    }

    /**
     * @throws KolaybiApiException
     * @throws KolaybiValidationException|ConnectionException
     */
    public function proceed(int $id, array $data): KolaybiResponse
    {
        $validated = $this->validate($data, [
            'amount' => 'required|numeric',
            'currency' => ['required', Rule::in(Currency::values())],
            'gateway_id' => 'required|integer',
            'gateway_type' => 'nullable|string|in:Vault,Associate',
            'exchange_rate' => 'nullable|numeric',
            'project_ids' => 'nullable|array',
            'project_ids.*' => 'integer',
            'description' => 'nullable|string',

            'expenses' => 'nullable|array',
            'expenses.*.source_type' => 'nullable|string|in:Associate,Vault',
            'expenses.*.amount' => 'nullable|numeric',
            'expenses.*.currency' => ['nullable', Rule::in(Currency::values())],
            'expenses.*.source_id' => 'nullable|integer',

            'transaction_currency_rates' => 'nullable|array',
            'transaction_currency_rates.*.quote_currency' => ['nullable', Rule::in(Currency::values())],
            'transaction_currency_rates.*.rate' => 'nullable|numeric',
            'transaction_currency_rates.*.total' => 'nullable|numeric',
        ]);

        return $this->client->post("/kolaybi/v1/associates/{$id}/transactions/proceed", $validated);
    }
}
