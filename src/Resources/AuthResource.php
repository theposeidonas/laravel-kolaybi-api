<?php

namespace Theposeidonas\Kolaybi\Resources;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Theposeidonas\Kolaybi\Exceptions\KolaybiApiException;

class AuthResource extends BaseResource
{
    public function getToken(): string
    {
        $config = $this->client->getConfig();
        $cacheKey = 'kolaybi_bearer_token_'.md5($config['api_key']);

        return Cache::remember($cacheKey, now()->addHours(23), function () use ($config) {
            $response = Http::withHeaders([
                'Channel' => $config['channel_id'],
            ])->post($config['base_url'].'/kolaybi/v1/access_token', [
                'api_key' => $config['api_key'],
            ]);

            if ($response->failed() || empty($response->json('data'))) {
                throw new KolaybiApiException(
                    'Authentication failed: '.($response->json('message') ?? $response->body()),
                    $response->status()
                );
            }

            return $response->json('data');
        });
    }
}
