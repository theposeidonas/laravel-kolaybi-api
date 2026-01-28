<?php

namespace Theposeidonas\Kolaybi\Resources;

use Illuminate\Support\Facades\Validator;
use Theposeidonas\Kolaybi\Exceptions\KolaybiValidationException;
use Theposeidonas\Kolaybi\KolaybiClient;

abstract class BaseResource
{
    public function __construct(protected KolaybiClient $client) {}

    /**
     * @throws KolaybiValidationException
     */
    protected function validate(array $data, array $rules): array
    {
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new KolaybiValidationException(
                'Validation failed: '.implode(', ', $validator->errors()->all())
            );
        }

        return $validator->validated();
    }
}
