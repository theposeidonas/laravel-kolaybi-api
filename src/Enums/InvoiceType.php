<?php

namespace Theposeidonas\Kolaybi\Enums;

use Theposeidonas\Kolaybi\Enums\Concerns\HasValues;

enum InvoiceType: string
{
    use HasValues;

    case SALE = 'sale_invoice';
    case SALE_RETURN = 'sale_return_invoice';
    case SALE_WAYBILL = 'sale_waybill';
    case PURCHASE = 'purchase_invoice';
    case PURCHASE_RETURN = 'purchase_return_invoice';
    case PURCHASE_WAYBILL = 'purchase_waybill';
    case GENERAL_EXPENSE = 'general_expense';
    case SELF_EMPLOYMENT = 'self_employment_receipt';
}
