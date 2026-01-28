<?php

namespace Theposeidonas\Kolaybi\Resources;

use Illuminate\Http\Client\ConnectionException;
use Theposeidonas\Kolaybi\Exceptions\KolaybiApiException;
use Theposeidonas\Kolaybi\Exceptions\KolaybiValidationException;
use Theposeidonas\Kolaybi\Responses\KolaybiResponse;

class TagResource extends BaseResource
{
    /**
     * @throws KolaybiApiException
     * @throws KolaybiValidationException
     * @throws ConnectionException
     */
    public function list(array $query = []): KolaybiResponse
    {
        $validated = $this->validate($query, [
            'group' => 'nullable|string',
        ]);

        return $this->client->get('/kolaybi/v1/tags', $validated);
    }

    /**
     * @throws KolaybiApiException
     * @throws ConnectionException
     */
    public function find(int $tagId): KolaybiResponse
    {
        return $this->client->get("/kolaybi/v1/tags/{$tagId}");
    }
}
