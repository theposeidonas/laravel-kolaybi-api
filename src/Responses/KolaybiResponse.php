<?php

namespace Theposeidonas\Kolaybi\Responses;

use Theposeidonas\Kolaybi\Exceptions\KolaybiApiException;
use Throwable;

class KolaybiResponse
{
    protected bool $success;

    protected mixed $data;

    protected ?string $message;

    protected ?array $error;

    protected int $status;

    /**
     * @throws KolaybiApiException
     */
    public function __construct(array $response, int $status)
    {
        try {
            $this->status = $status;
            $this->success = $response['success'] ?? ($status >= 200 && $status < 300);
            $this->data = $response['data'] ?? null;
            $this->message = $response['message'] ?? null;
            $this->error = $response['error'] ?? null;

            if (! $this->success) {
                $errorMessage = $this->error['message'] ?? ($this->message ?? 'Unknown API Error');
                throw new KolaybiApiException($errorMessage, $this->status);
            }
        } catch (Throwable $e) {
            if ($e instanceof KolaybiApiException) {
                throw $e;
            }

            throw new KolaybiApiException($e->getMessage(), $this->status);
        }
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getStatus(): int
    {
        return $this->status;
    }
}
