<?php

namespace Theposeidonas\Kolaybi\Resources;

use BackedEnum;
use Illuminate\Support\Facades\Validator;
use Theposeidonas\Kolaybi\Exceptions\KolaybiValidationException;
use Theposeidonas\Kolaybi\KolaybiClient;

abstract class BaseResource
{
    public function __construct(protected readonly KolaybiClient $client) {}

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $rules
     * @return array<string, mixed>
     *
     * @throws KolaybiValidationException
     */
    protected function validate(array $data, array $rules): array
    {
        $validator = Validator::make($this->normalize($data), $rules);

        if ($validator->fails()) {
            throw new KolaybiValidationException(
                'Validation failed: '.implode(', ', $validator->errors()->all()),
                $validator->errors()->toArray(),
            );
        }

        return $validator->validated();
    }

    /**
     * @param  array<array-key, mixed>  $data
     * @return array<array-key, mixed>
     */
    protected function normalize(array $data): array
    {
        return array_map(fn (mixed $value): mixed => match (true) {
            $value instanceof BackedEnum => $value->value,
            $value instanceof \DateTimeInterface => $value->format('Y-m-d'),
            is_array($value) => $this->normalize($value),
            default => $value,
        }, $data);
    }
}
