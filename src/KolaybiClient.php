<?php

namespace Theposeidonas\Kolaybi;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Theposeidonas\Kolaybi\Exceptions\KolaybiApiException;
use Theposeidonas\Kolaybi\Resources\AssociateResource;
use Theposeidonas\Kolaybi\Resources\AuthResource;
use Theposeidonas\Kolaybi\Resources\BankResource;
use Theposeidonas\Kolaybi\Resources\BaseResource;
use Theposeidonas\Kolaybi\Resources\BondResource;
use Theposeidonas\Kolaybi\Resources\ChequeResource;
use Theposeidonas\Kolaybi\Resources\CompanyResource;
use Theposeidonas\Kolaybi\Resources\FinancialActionTypeResource;
use Theposeidonas\Kolaybi\Resources\InvoiceResource;
use Theposeidonas\Kolaybi\Resources\OnlineBankResource;
use Theposeidonas\Kolaybi\Resources\OrderResource;
use Theposeidonas\Kolaybi\Resources\ProductResource;
use Theposeidonas\Kolaybi\Resources\ProformaResource;
use Theposeidonas\Kolaybi\Resources\TagResource;
use Theposeidonas\Kolaybi\Resources\UserResource;
use Theposeidonas\Kolaybi\Responses\KolaybiResponse;
use Throwable;

class KolaybiClient
{
    /**
     * @var array<class-string<BaseResource>, BaseResource>
     */
    protected array $resources = [];

    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(protected array $config) {}

    /**
     * @return array<string, mixed>
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public function withConfig(array $config): static
    {
        return new static(array_replace_recursive($this->config, $config));
    }

    public function auth(): AuthResource
    {
        return $this->resource(AuthResource::class);
    }

    public function bank(): BankResource
    {
        return $this->resource(BankResource::class);
    }

    public function company(): CompanyResource
    {
        return $this->resource(CompanyResource::class);
    }

    public function associate(): AssociateResource
    {
        return $this->resource(AssociateResource::class);
    }

    public function invoice(): InvoiceResource
    {
        return $this->resource(InvoiceResource::class);
    }

    public function tag(): TagResource
    {
        return $this->resource(TagResource::class);
    }

    public function order(): OrderResource
    {
        return $this->resource(OrderResource::class);
    }

    public function product(): ProductResource
    {
        return $this->resource(ProductResource::class);
    }

    public function proforma(): ProformaResource
    {
        return $this->resource(ProformaResource::class);
    }

    public function user(): UserResource
    {
        return $this->resource(UserResource::class);
    }

    public function cheque(): ChequeResource
    {
        return $this->resource(ChequeResource::class);
    }

    public function bond(): BondResource
    {
        return $this->resource(BondResource::class);
    }

    public function onlineBank(): OnlineBankResource
    {
        return $this->resource(OnlineBankResource::class);
    }

    public function financialActionType(): FinancialActionTypeResource
    {
        return $this->resource(FinancialActionTypeResource::class);
    }

    /**
     * @param  array<string, mixed>  $query
     *
     * @throws KolaybiApiException|ConnectionException
     */
    public function get(string $url, array $query = []): KolaybiResponse
    {
        return $this->send('GET', $url, $query);
    }

    /**
     * @param  array<string, mixed>  $data
     *
     * @throws KolaybiApiException|ConnectionException
     */
    public function post(string $url, array $data = []): KolaybiResponse
    {
        return $this->send('POST', $url, $data);
    }

    /**
     * @param  array<string, mixed>  $data
     *
     * @throws KolaybiApiException|ConnectionException
     */
    public function put(string $url, array $data = []): KolaybiResponse
    {
        return $this->send('PUT', $url, $data);
    }

    /**
     * @param  array<string, mixed>  $data
     *
     * @throws KolaybiApiException|ConnectionException
     */
    public function patch(string $url, array $data = []): KolaybiResponse
    {
        return $this->send('PATCH', $url, $data);
    }

    /**
     * @param  array<string, mixed>  $query
     *
     * @throws KolaybiApiException|ConnectionException
     */
    public function delete(string $url, array $query = []): KolaybiResponse
    {
        return $this->send('DELETE', $url, $query);
    }

    /**
     * @param  array<string, mixed>  $payload
     *
     * @throws KolaybiApiException|ConnectionException
     */
    public function send(string $method, string $url, array $payload = []): KolaybiResponse
    {
        $response = $this->dispatch($method, $url, $payload);

        if ($response->status() === 401) {
            $this->auth()->forgetToken();
            $response = $this->dispatch($method, $url, $payload);
        }

        return KolaybiResponse::fromHttpResponse($response);
    }

    public function http(): PendingRequest
    {
        $retry = $this->config['retry'] ?? [];

        return Http::baseUrl(rtrim((string) $this->config['base_url'], '/'))
            ->withHeaders(['Channel' => (string) ($this->config['channel_id'] ?? '')])
            ->acceptJson()
            ->timeout((int) ($this->config['timeout'] ?? 30))
            ->connectTimeout((int) ($this->config['connect_timeout'] ?? 10))
            ->retry(
                max(1, (int) ($retry['times'] ?? 0) + 1),
                (int) ($retry['sleep'] ?? 200),
                fn (Throwable $e): bool => $e instanceof ConnectionException
                    || ($e instanceof RequestException && $e->response->serverError()),
                throw: false,
            );
    }

    /**
     * @param  array<string, mixed>  $payload
     *
     * @throws KolaybiApiException|ConnectionException
     */
    protected function dispatch(string $method, string $url, array $payload): Response
    {
        $options = in_array($method, ['GET', 'DELETE'], true)
            ? ['query' => $payload]
            : ['json' => $payload];

        return $this->http()
            ->withToken($this->auth()->getToken())
            ->send($method, '/'.ltrim($url, '/'), $payload === [] ? [] : $options);
    }

    /**
     * @template T of BaseResource
     *
     * @param  class-string<T>  $class
     * @return T
     */
    protected function resource(string $class): BaseResource
    {
        return $this->resources[$class] ??= new $class($this);
    }
}
