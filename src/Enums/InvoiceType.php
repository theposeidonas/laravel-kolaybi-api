<?php

namespace Theposeidonas\Kolaybi\Enums;

enum InvoiceType: string
{
    case SALE = 'sale_invoice';
    case SALE_RETURN = 'sale_return_invoice';
    case PURCHASE = 'purchase_invoice';
    case PURCHASE_RETURN = 'purchase_return_invoice';
    case SELF_EMPLOYMENT = 'self_employment_receipt';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
