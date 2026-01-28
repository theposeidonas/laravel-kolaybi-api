<?php

namespace Theposeidonas\Kolaybi\Facades;

use Illuminate\Support\Facades\Facade;
use Theposeidonas\Kolaybi\Resources\AuthResource;
use Theposeidonas\Kolaybi\Resources\BankResource;
use Theposeidonas\Kolaybi\Resources\CompanyResource;
use Theposeidonas\Kolaybi\Resources\CustomerResource;
use Theposeidonas\Kolaybi\Resources\InvoiceResource;
use Theposeidonas\Kolaybi\Resources\OrderResource;
use Theposeidonas\Kolaybi\Resources\ProductResource;
use Theposeidonas\Kolaybi\Resources\ProformaResource;
use Theposeidonas\Kolaybi\Resources\TagResource;
use Theposeidonas\Kolaybi\Resources\UserResource;

/**
 * @method static AuthResource auth()
 * @method static BankResource bank()
 * @method static CompanyResource company()
 * @method static CustomerResource customer()
 * @method static InvoiceResource invoice()
 * @method static TagResource tag()
 * @method static OrderResource order()
 * @method static ProductResource product()
 * @method static ProformaResource proforma()
 * @method static UserResource user()
 *
 * * @see \Theposeidonas\Kolaybi\KolaybiClient
 */
class Kolaybi extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Theposeidonas\Kolaybi\KolaybiClient::class;
    }
}
