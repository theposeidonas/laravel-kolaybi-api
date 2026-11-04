<?php

namespace Theposeidonas\Kolaybi\Resources;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Validation\Rule;
use Theposeidonas\Kolaybi\Enums\BondStatus;
use Theposeidonas\Kolaybi\Enums\CashFlowDirection;
use Theposeidonas\Kolaybi\Enums\Currency;
use Theposeidonas\Kolaybi\Exceptions\KolaybiApiException;
use Theposeidonas\Kolaybi\Exceptions\KolaybiValidationException;
use Theposeidonas\Kolaybi\Responses\KolaybiResponse;

class BondResource extends BaseResource
{
    /**
     * @param  array<string, mixed>  $query
     *
     * @throws KolaybiApiException|KolaybiValidationException|ConnectionException
     */
    public function list(array $query = []): KolaybiResponse
    {
        $validated = $this->validate($query, [
            'bond_status' => ['nullable', Rule::in(BondStatus::values())],
            'cash_flow_direction' => ['nullable', 'integer', Rule::in(CashFlowDirection::values())],
            'bond_id' => 'nullable|integer',
            'serial_no' => 'nullable|string',
            'drawer' => 'nullable|string',
            'payee' => 'nullable|string',
            'guarantor' => 'nullable|string',
            'associate_id' => 'nullable|integer',
            'currency' => ['nullable', Rule::in(Currency::values())],
            'min_amount' => 'nullable|numeric',
            'max_amount' => 'nullable|numeric|gte:min_amount',
            'min_issue_date' => 'nullable|date_format:Y-m-d',
            'max_issue_date' => 'nullable|date_format:Y-m-d|after_or_equal:min_issue_date',
            'receive_start_date' => 'nullable|date_format:Y-m-d',
            'receive_end_date' => 'nullable|date_format:Y-m-d|after_or_equal:receive_start_date',
        ]);

        return $this->client->get('/kolaybi/v1/bonds', $validated);
    }

    /**
     * @throws KolaybiApiException|ConnectionException
     */
    public function find(int $bondId): KolaybiResponse
    {
        return $this->client->get("/kolaybi/v1/bonds/{$bondId}");
    }
}
