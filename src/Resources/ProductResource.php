<?php

namespace Theposeidonas\Kolaybi\Resources;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Validation\Rule;
use Theposeidonas\Kolaybi\Enums\Currency;
use Theposeidonas\Kolaybi\Enums\ProductType;
use Theposeidonas\Kolaybi\Enums\VATRate;
use Theposeidonas\Kolaybi\Exceptions\KolaybiApiException;
use Theposeidonas\Kolaybi\Exceptions\KolaybiValidationException;
use Theposeidonas\Kolaybi\Responses\KolaybiResponse;

class ProductResource extends BaseResource
{
    /**
     * @throws KolaybiApiException|KolaybiValidationException|ConnectionException
     */
    public function list(array $query = []): KolaybiResponse
    {
        $validated = $this->validate($query, [
            'barcode' => 'nullable|string',
            'code' => 'nullable|string',
            'name' => 'nullable|string',
            'type' => ['nullable', Rule::in(ProductType::values())],
            'group' => 'nullable|string',
            'full_text_search' => 'nullable|string',
        ]);

        return $this->client->get('/kolaybi/v1/products', $validated);
    }

    /**
     * @throws KolaybiApiException|KolaybiValidationException|ConnectionException
     */
    public function create(array $data): KolaybiResponse
    {
        $validated = $this->validate($data, [
            'name' => 'required|string',
            'code' => 'nullable|string',
            'barcode' => 'nullable|string',
            'description' => 'nullable|string',
            'product_type' => ['nullable', Rule::in(ProductType::values())],
            'vat_rate' => ['nullable', Rule::in(VATRate::values())],
            'price' => 'nullable|numeric',
            'price_currency' => ['nullable', Rule::in(Currency::values())],
            'quantity' => 'nullable|numeric',
            'discount_type' => 'nullable|string|in:percentage,numeric',
            'purchase_price_vat_included' => 'nullable|boolean',
            'sale_price_vat_included' => 'nullable|boolean',
            'discount_value' => 'nullable|numeric',
            'tags' => 'nullable|array',
            'tags.*' => 'integer',
        ]);

        return $this->client->post('/kolaybi/v1/products', $validated);
    }

    /**
     * @throws KolaybiApiException|ConnectionException
     */
    public function find(int $productId): KolaybiResponse
    {
        return $this->client->get("/kolaybi/v1/products/{$productId}");
    }

    /**
     * @throws KolaybiApiException|KolaybiValidationException|ConnectionException
     */
    public function update(int $productId, array $data): KolaybiResponse
    {
        $validated = $this->validate($data, [
            'name' => 'required|string',
            'code' => 'nullable|string',
            'barcode' => 'nullable|string',
            'description' => 'nullable|string',
            'product_type' => ['nullable', Rule::in(ProductType::values())],
            'vat_rate' => ['nullable', Rule::in(VATRate::values())],
            'price' => 'nullable|numeric',
            'price_currency' => ['nullable', Rule::in(Currency::values())],
            'quantity' => 'nullable|numeric',
            'discount_type' => 'nullable|string|in:percentage,numeric',
            'discount_value' => 'nullable|numeric',
            'tags' => 'nullable|array',
            'tags.*' => 'integer',
        ]);

        return $this->client->put("/kolaybi/v1/products/{$productId}", $validated);
    }

    /**
     * @throws KolaybiApiException|KolaybiValidationException|ConnectionException
     */
    public function stock(array $data): KolaybiResponse
    {
        $validated = $this->validate($data, [
            'product_id' => 'required|integer',
            'quantity' => 'required|numeric',
            'stock_flow_direction' => ['required', 'integer', Rule::in([1, -1])],
            'description' => 'nullable|string',
            'unit_amount' => 'nullable|numeric',
            'currency' => ['nullable', Rule::in(Currency::values())],
            'issue_date' => 'nullable|string',
        ]);

        return $this->client->post('/kolaybi/v1/stock_histories', $validated);
    }
}
