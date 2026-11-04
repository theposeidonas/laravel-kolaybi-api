<?php

namespace Theposeidonas\Kolaybi\Enums;

use Theposeidonas\Kolaybi\Enums\Concerns\HasValues;

enum AddressType: string
{
    use HasValues;

    case DELIVERY = 'delivery';
    case INVOICE = 'invoice';
    case SHIPMENT = 'shipment';
    case STOCK = 'stock';
    case COMPANY = 'company';
    case ASSOCIATE = 'associate';
    case OTHER = 'other';
}
