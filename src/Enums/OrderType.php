<?php

namespace Theposeidonas\Kolaybi\Enums;

use Theposeidonas\Kolaybi\Enums\Concerns\HasValues;

enum OrderType: string
{
    use HasValues;

    case SALE = 'sale_order';
    case PURCHASE = 'purchase_order';
}
