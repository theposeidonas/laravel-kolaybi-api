<?php

namespace Theposeidonas\Kolaybi\Exceptions;

use Throwable;

class KolaybiApiException extends KolaybiException
{
    /**
     * @param  array<array-key, mixed>  $response
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
        protected array $response = [],
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getStatus(): int
    {
        return $this->getCode();
    }

    /**
     * @return array<array-key, mixed>
     */
    public function getResponse(): array
    {
        return $this->response;
    }

    public function getError(): mixed
    {
        return $this->response['error'] ?? null;
    }
}
