<?php

namespace Theposeidonas\Kolaybi\Enums;

use Theposeidonas\Kolaybi\Enums\Concerns\HasValues;

enum StoppageName: string
{
    use HasValues;

    case INCOME_TAX = 'income_tax';
    case CORPORATE_TAX = 'corporate_tax';
}
