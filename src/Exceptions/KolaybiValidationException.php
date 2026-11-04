<?php

namespace Theposeidonas\Kolaybi\Exceptions;

use Throwable;

class KolaybiValidationException extends KolaybiException
{
    /**
     * @param  array<string, list<string>>  $errors
     */
    public function __construct(
        string $message = '',
        protected array $errors = [],
        int $code = 422,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array<string, list<string>>
     */
    public function errors(): array
    {
        return $this->errors;
    }
}
