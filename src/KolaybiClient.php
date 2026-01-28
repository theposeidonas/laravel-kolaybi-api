<?php

namespace Theposeidonas\Kolaybi;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Theposeidonas\Kolaybi\Exceptions\KolaybiApiException;
use Theposeidonas\Kolaybi\Resources\AuthResource;
use Theposeidonas\Kolaybi\Resources\BankResource;
use Theposeidonas\Kolaybi\Resources\CompanyResource;
use Theposeidonas\Kolaybi\Resources\AssociateResource;
use Theposeidonas\Kolaybi\Resources\InvoiceResource;
use Theposeidonas\Kolaybi\Resources\OrderResource;
use Theposeidonas\Kolaybi\Resources\ProductResource;
use Theposeidonas\Kolaybi\Resources\ProformaResource;
use Theposeidonas\Kolaybi\Resources\TagResource;
use Theposeidonas\Kolaybi\Resources\UserResource;
use Theposeidonas\Kolaybi\Responses\KolaybiResponse;

class KolaybiClient
{
    public function __construct(protected array $config) {}

    public function getConfig(): array
    {
        return $this->config;
    }

    public function auth(): AuthResource
    {
        return new AuthResource($this);
    }

    public function bank(): BankResource
    {
        return new BankResource($this);
    }

    public function company(): CompanyResource
    {
        return new CompanyResource($this);
    }

    public function associate(): AssociateResource
    {
        return new AssociateResource($this);
    }

    public function invoice(): InvoiceResource
    {
        return new InvoiceResource($this);
    }

    public function tag(): TagResource
    {
        return new TagResource($this);
    }

    public function order(): OrderResource
    {
        return new OrderResource($this);
    }

    public function product(): ProductResource
    {
        return new ProductResource($this);
    }

    public function proforma(): ProformaResource
    {
        return new ProformaResource($this);
    }

    public function user(): UserResource
    {
        return new UserResource($this);
    }

    /**
     * @throws KolaybiApiException|ConnectionException
     */
    public function get(string $url, array $query = []): KolaybiResponse
    {
        $response = $this->buildRequest()->get($url, $query);

        return new KolaybiResponse($response->json() ?? [], $response->status());
    }

    /**
     * @throws KolaybiApiException|ConnectionException
     */
    public function post(string $url, array $data = []): KolaybiResponse
    {
        $response = $this->buildRequest()->post($url, $data);

        return new KolaybiResponse($response->json() ?? [], $response->status());
    }

    /**
     * @throws KolaybiApiException|ConnectionException
     */
    public function put(string $url, array $data = []): KolaybiResponse
    {
        $response = $this->buildRequest()->put($url, $data);

        return new KolaybiResponse($response->json() ?? [], $response->status());
    }

    /**
     * @throws KolaybiApiException
     * @throws ConnectionException
     */
    public function delete(string $url, array $query = []): KolaybiResponse
    {
        $response = $this->buildRequest()->delete($url, $query);

        return new KolaybiResponse($response->json() ?? [], $response->status());
    }

    protected function buildRequest(): PendingRequest
    {
        return Http::withToken($this->auth()->getToken())
            ->baseUrl($this->config['base_url'])
            ->withHeaders(['Channel' => $this->config['channel_id']])
            ->acceptJson();
    }
}
