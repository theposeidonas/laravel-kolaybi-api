<?php

namespace Theposeidonas\Kolaybi\Responses;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use JsonSerializable;
use LogicException;
use Theposeidonas\Kolaybi\Exceptions\KolaybiApiException;

/**
 * @implements ArrayAccess<array-key, mixed>
 * @implements Arrayable<array-key, mixed>
 */
class KolaybiResponse implements Arrayable, ArrayAccess, Jsonable, JsonSerializable
{
    protected bool $success;

    protected mixed $data;

    protected ?string $message;

    protected mixed $error;

    /**
     * @param  array<array-key, mixed>  $response
     *
     * @throws KolaybiApiException
     */
    public function __construct(protected array $response, protected int $status)
    {
        $this->success = (bool) ($response['success'] ?? ($status >= 200 && $status < 300));
        $this->data = $response['data'] ?? null;
        $this->message = is_string($response['message'] ?? null) ? $response['message'] : null;
        $this->error = $response['error'] ?? null;

        if (! $this->success) {
            throw new KolaybiApiException($this->resolveErrorMessage(), $this->status, null, $this->response);
        }
    }

    /**
     * @throws KolaybiApiException
     */
    public static function fromHttpResponse(Response $response): static
    {
        $json = $response->json();

        if (! is_array($json)) {
            $json = $response->successful() ? ['data' => $response->body()] : ['message' => $response->body() ?: $response->reason()];
        }

        return new static($json, $response->status());
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getData(string|int|null $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->data;
        }

        return is_array($this->data) ? Arr::get($this->data, $key, $default) : $default;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getError(): mixed
    {
        return $this->error;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return array<array-key, mixed>
     */
    public function getRaw(): array
    {
        return $this->response;
    }

    /**
     * @return array<array-key, mixed>
     */
    public function toArray(): array
    {
        return $this->response;
    }

    /**
     * @return array<array-key, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->response;
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->response, $options | JSON_THROW_ON_ERROR);
    }

    public function offsetExists(mixed $offset): bool
    {
        return is_array($this->data) && Arr::has($this->data, $offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->getData($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): never
    {
        throw new LogicException('KolaybiResponse is immutable.');
    }

    public function offsetUnset(mixed $offset): never
    {
        throw new LogicException('KolaybiResponse is immutable.');
    }

    protected function resolveErrorMessage(): string
    {
        $error = $this->error;

        if (is_array($error) && is_string($error['message'] ?? null)) {
            return $error['message'];
        }

        if (is_string($error) && $error !== '') {
            return $error;
        }

        $message = $this->response['message'] ?? null;

        if (is_array($message) && $message !== []) {
            return implode(', ', array_map(fn (mixed $value): string => is_scalar($value) ? (string) $value : (string) json_encode($value), Arr::flatten($message)));
        }

        return $this->message ?? 'Unknown API Error';
    }
}
