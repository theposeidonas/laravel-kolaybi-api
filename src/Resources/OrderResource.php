<?php

namespace Theposeidonas\Kolaybi\Resources;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Validation\Rule;
use Theposeidonas\Kolaybi\Enums\Currency;
use Theposeidonas\Kolaybi\Enums\OrderType;
use Theposeidonas\Kolaybi\Enums\VATRate;
use Theposeidonas\Kolaybi\Exceptions\KolaybiApiException;
use Theposeidonas\Kolaybi\Exceptions\KolaybiValidationException;
use Theposeidonas\Kolaybi\Responses\KolaybiResponse;

class OrderResource extends BaseResource
{
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
            'type' => ['nullable', Rule::in(OrderType::values())],
            'serial_no' => 'nullable|string',
            'due_date' => 'nullable|date',
            'description' => 'nullable|string',
            'tracking_currency' => ['nullable', Rule::in(Currency::values())],
            'tags' => 'nullable|array',
            'subtotal_discount_amount' => 'nullable|numeric',
            'exchange_rate' => 'nullable|string',

            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer',
            'items.*.quantity' => 'required|string',
            'items.*.unit_price' => 'required|string',
            'items.*.vat_rate' => ['required', Rule::in(VATRate::values())],
            'items.*.description' => 'nullable|string',
            'items.*.discount_amount' => 'nullable|numeric',
        ]);

        return $this->client->post('/kolaybi/v1/orders', $validated);
    }

    /**
     * @throws KolaybiApiException
     * @throws ConnectionException
     */
    public function find(int $documentId): KolaybiResponse
    {
        return $this->client->get("/kolaybi/v1/orders/{$documentId}");
    }
}
