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

    /**
     * @param  list<int>  $tagIds
     *
     * @throws KolaybiApiException|KolaybiValidationException|ConnectionException
     */
    public function sync(string $type, int $id, array $tagIds): KolaybiResponse
    {
        $validated = $this->validate(['type' => $type, 'tag_ids' => $tagIds], [
            'type' => 'required|string',
            'tag_ids' => 'present|array',
            'tag_ids.*' => 'integer',
        ]);

        return $this->client->put("/kolaybi/v1/tags/{$validated['type']}/{$id}", [
            'relations' => [
                'tags' => array_map(fn (int|string $tagId): array => ['id' => (int) $tagId], array_values($validated['tag_ids'])),
            ],
        ]);
    }
}
