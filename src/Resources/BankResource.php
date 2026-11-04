<?php

namespace Theposeidonas\Kolaybi\Resources;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Validation\Rule;
use Theposeidonas\Kolaybi\Enums\VaultType;
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
            'type' => ['nullable', 'string', Rule::in(VaultType::values())],
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
