<?php

namespace Theposeidonas\Kolaybi\Resources;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Theposeidonas\Kolaybi\Exceptions\KolaybiApiException;

class AuthResource extends BaseResource
{
    /**
     * @throws KolaybiApiException|ConnectionException
     */
    public function getToken(): string
    {
        $config = $this->client->getConfig();
        $ttl = (int) ($config['cache']['ttl'] ?? 82800);

        return $this->cache()->remember($this->cacheKey(), $ttl, fn (): string => $this->requestToken());
    }

    /**
     * @throws KolaybiApiException|ConnectionException
     */
    public function refreshToken(): string
    {
        $this->forgetToken();

        return $this->getToken();
    }

    public function forgetToken(): bool
    {
        return $this->cache()->forget($this->cacheKey());
    }

    /**
     * @throws KolaybiApiException|ConnectionException
     */
    protected function requestToken(): string
    {
        $config = $this->client->getConfig();

        if (blank($config['api_key'] ?? null) || blank($config['channel_id'] ?? null)) {
            throw new KolaybiApiException('KolayBi api_key and channel_id must be configured.', 401);
        }

        $response = $this->client->http()->post('/kolaybi/v1/access_token', [
            'api_key' => $config['api_key'],
        ]);

        $token = $response->json('data');

        if ($response->failed() || ! is_string($token) || $token === '') {
            throw new KolaybiApiException(
                'Authentication failed: '.($response->json('message') ?? $response->body()),
                $response->status(),
                null,
                (array) ($response->json() ?? []),
            );
        }

        return $token;
    }

    protected function cacheKey(): string
    {
        $config = $this->client->getConfig();
        $prefix = $config['cache']['prefix'] ?? 'kolaybi_bearer_token_';

        return $prefix.hash('xxh128', ($config['base_url'] ?? '').'|'.($config['channel_id'] ?? '').'|'.($config['api_key'] ?? ''));
    }

    protected function cache(): Repository
    {
        return Cache::store($this->client->getConfig()['cache']['store'] ?? null);
    }
}
