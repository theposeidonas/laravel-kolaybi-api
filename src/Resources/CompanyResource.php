<?php

namespace Theposeidonas\Kolaybi\Resources;

use Illuminate\Http\Client\ConnectionException;
use Theposeidonas\Kolaybi\Exceptions\KolaybiApiException;
use Theposeidonas\Kolaybi\Responses\KolaybiResponse;

class CompanyResource extends BaseResource
{
    /**
     * @throws KolaybiApiException|ConnectionException
     */
    public function list(): KolaybiResponse
    {
        return $this->client->get('/kolaybi/v1/companies');
    }
}
