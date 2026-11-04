<?php

namespace Theposeidonas\Kolaybi\Resources;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Validation\Rule;
use Theposeidonas\Kolaybi\Enums\CashFlowDirection;
use Theposeidonas\Kolaybi\Enums\ChequeStatus;
use Theposeidonas\Kolaybi\Enums\Currency;
use Theposeidonas\Kolaybi\Exceptions\KolaybiApiException;
use Theposeidonas\Kolaybi\Exceptions\KolaybiValidationException;
use Theposeidonas\Kolaybi\Responses\KolaybiResponse;

class ChequeResource extends BaseResource
{
    /**
     * @param  array<string, mixed>  $query
     *
     * @throws KolaybiApiException|KolaybiValidationException|ConnectionException
     */
    public function list(array $query = []): KolaybiResponse
    {
        $validated = $this->validate($query, [
            'cheque_status' => ['nullable', Rule::in(ChequeStatus::values())],
            'cash_flow_direction' => ['nullable', 'integer', Rule::in(CashFlowDirection::values())],
            'serial_no' => 'nullable|string',
            'drawer' => 'nullable|string',
            'payee' => 'nullable|string',
            'associate_id' => 'nullable|integer',
            'endorsed_associate_id' => 'nullable|integer',
            'currency' => ['nullable', Rule::in(Currency::values())],
            'min_amount' => 'nullable|numeric',
            'max_amount' => 'nullable|numeric|gte:min_amount',
            'min_issue_date' => 'nullable|date_format:Y-m-d',
            'max_issue_date' => 'nullable|date_format:Y-m-d|after_or_equal:min_issue_date',
            'min_receive_date' => 'nullable|date_format:Y-m-d',
            'max_receive_date' => 'nullable|date_format:Y-m-d|after_or_equal:min_receive_date',
        ]);

        return $this->client->get('/kolaybi/v1/cheques', $validated);
    }

    /**
     * @throws KolaybiApiException|ConnectionException
     */
    public function find(int $chequeId): KolaybiResponse
    {
        return $this->client->get("/kolaybi/v1/cheques/{$chequeId}");
    }
}
