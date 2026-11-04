<?php

namespace Theposeidonas\Kolaybi\Resources;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Validation\Rule;
use Theposeidonas\Kolaybi\Enums\TransactionDirection;
use Theposeidonas\Kolaybi\Exceptions\KolaybiApiException;
use Theposeidonas\Kolaybi\Exceptions\KolaybiValidationException;
use Theposeidonas\Kolaybi\Responses\KolaybiResponse;

class OnlineBankResource extends BaseResource
{
    /**
     * @throws KolaybiApiException|ConnectionException
     */
    public function banks(): KolaybiResponse
    {
        return $this->client->get('/kolaybi/v1/online_bank/banks');
    }

    /**
     * @throws KolaybiApiException|ConnectionException
     */
    public function bankAccounts(string $bankId): KolaybiResponse
    {
        return $this->client->get('/kolaybi/v1/online_bank/banks/'.rawurlencode($bankId).'/accounts');
    }

    /**
     * @param  array<string, mixed>  $query
     *
     * @throws KolaybiApiException|KolaybiValidationException|ConnectionException
     */
    public function bankTransactions(string $bankId, array $query = []): KolaybiResponse
    {
        return $this->client->get(
            '/kolaybi/v1/online_bank/banks/'.rawurlencode($bankId).'/transactions',
            $this->validateTransactionQuery($query),
        );
    }

    /**
     * @throws KolaybiApiException|ConnectionException
     */
    public function accounts(): KolaybiResponse
    {
        return $this->client->get('/kolaybi/v1/online_bank/accounts');
    }

    /**
     * @throws KolaybiApiException|ConnectionException
     */
    public function account(string $accountId): KolaybiResponse
    {
        return $this->client->get('/kolaybi/v1/online_bank/accounts/'.rawurlencode($accountId));
    }

    /**
     * @param  array<string, mixed>  $query
     *
     * @throws KolaybiApiException|KolaybiValidationException|ConnectionException
     */
    public function accountTransactions(string $accountId, array $query = []): KolaybiResponse
    {
        return $this->client->get(
            '/kolaybi/v1/online_bank/accounts/'.rawurlencode($accountId).'/transactions',
            $this->validateTransactionQuery($query),
        );
    }

    /**
     * @throws KolaybiApiException|ConnectionException
     */
    public function transaction(string $transactionId): KolaybiResponse
    {
        return $this->client->get('/kolaybi/v1/online_bank/transactions/'.rawurlencode($transactionId));
    }

    /**
     * @param  array<string, mixed>  $data
     *
     * @throws KolaybiApiException|KolaybiValidationException|ConnectionException
     */
    public function map(string $transactionId, array $data): KolaybiResponse
    {
        $validated = $this->validate($data, [
            'vault_id' => 'required|integer',
            'invoices' => 'nullable|array',
            'invoices.*.id' => 'required|integer',
            'invoices.*.amount' => 'required|numeric',
            'contacts' => 'nullable|array',
            'contacts.*.id' => 'required|integer',
            'contacts.*.amount' => 'required|numeric',
            'cheques' => 'nullable|array',
            'cheques.*.id' => 'required|integer',
            'cheques.*.amount' => 'required|numeric',
            'commission_contact_id' => 'nullable|integer',
            'exchange_rate' => 'nullable|numeric',
            'project_id' => 'nullable|integer',
        ]);

        return $this->client->post('/kolaybi/v1/online_bank/transactions/'.rawurlencode($transactionId).'/map', $validated);
    }

    /**
     * @throws KolaybiApiException|ConnectionException
     */
    public function unmap(string $transactionId): KolaybiResponse
    {
        return $this->client->delete('/kolaybi/v1/online_bank/transactions/'.rawurlencode($transactionId).'/map');
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     *
     * @throws KolaybiValidationException
     */
    protected function validateTransactionQuery(array $query): array
    {
        $validated = $this->validate($query, [
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
            'min_amount' => 'nullable|numeric',
            'max_amount' => 'nullable|numeric|gte:min_amount',
            'receipt_no' => 'nullable|string',
            'explanation' => 'nullable|string',
            'direction' => ['nullable', Rule::in(TransactionDirection::values())],
            'is_mapped' => 'nullable|boolean',
        ]);

        if (array_key_exists('is_mapped', $validated) && $validated['is_mapped'] !== null) {
            $validated['is_mapped'] = filter_var($validated['is_mapped'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        }

        return $validated;
    }
}
