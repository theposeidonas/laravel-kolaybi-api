<?php

namespace Theposeidonas\Kolaybi\Facades;

use Illuminate\Support\Facades\Facade;
use Theposeidonas\Kolaybi\KolaybiClient;
use Theposeidonas\Kolaybi\Resources\AssociateResource;
use Theposeidonas\Kolaybi\Resources\AuthResource;
use Theposeidonas\Kolaybi\Resources\BankResource;
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

/**
 * @method static AuthResource auth()
 * @method static BankResource bank()
 * @method static CompanyResource company()
 * @method static AssociateResource associate()
 * @method static InvoiceResource invoice()
 * @method static TagResource tag()
 * @method static OrderResource order()
 * @method static ProductResource product()
 * @method static ProformaResource proforma()
 * @method static UserResource user()
 * @method static ChequeResource cheque()
 * @method static BondResource bond()
 * @method static OnlineBankResource onlineBank()
 * @method static FinancialActionTypeResource financialActionType()
 * @method static KolaybiClient withConfig(array $config)
 * @method static array getConfig()
 * @method static KolaybiResponse get(string $url, array $query = [])
 * @method static KolaybiResponse post(string $url, array $data = [])
 * @method static KolaybiResponse put(string $url, array $data = [])
 * @method static KolaybiResponse patch(string $url, array $data = [])
 * @method static KolaybiResponse delete(string $url, array $query = [])
 * @method static KolaybiResponse send(string $method, string $url, array $payload = [])
 *
 * @see KolaybiClient
 */
class Kolaybi extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return KolaybiClient::class;
    }
}
