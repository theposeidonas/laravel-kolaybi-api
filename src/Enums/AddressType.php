<?php

namespace Theposeidonas\Kolaybi\Enums;

enum AddressType: string
{
    case DELIVERY = 'delivery';
    case INVOICE = 'invoice';
    case SHIPMENT = 'shipment';
    case STOCK = 'stock';
    case COMPANY = 'company';
    case ASSOCIATE = 'associate';
    case OTHER = 'other';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
