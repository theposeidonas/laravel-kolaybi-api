<?php

namespace Theposeidonas\Kolaybi\Resources;

use Illuminate\Http\Client\ConnectionException;
use Theposeidonas\Kolaybi\Exceptions\KolaybiApiException;
use Theposeidonas\Kolaybi\Exceptions\KolaybiValidationException;
use Theposeidonas\Kolaybi\Responses\KolaybiResponse;

class BankResource extends BaseResource
{
    /**
     * @throws KolaybiApiException
     * @throws KolaybiValidationException
     * @throws ConnectionException
     */
    public function list(array $query = []): KolaybiResponse
    {
        $validated = $this->validate($query, [
            'currency' => 'nullable|string',
            'type' => 'nullable|string|in:bank_account,safe_deposit',
        ]);

        return $this->client->get('/kolaybi/v1/vaults', $validated);
    }

    /**
     * @throws KolaybiApiException
     * @throws ConnectionException
     */
    public function find(int $vaultId): KolaybiResponse
    {
        return $this->client->get("/kolaybi/v1/vaults/{$vaultId}");
    }

    /**
     * @throws KolaybiApiException
     * @throws ConnectionException
     */
    public function transactions(int $vaultId): KolaybiResponse
    {
        return $this->client->get("/kolaybi/v1/vaults/{$vaultId}/transactions");
    }
}
