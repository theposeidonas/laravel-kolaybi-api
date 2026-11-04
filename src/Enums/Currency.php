<?php

namespace Theposeidonas\Kolaybi\Enums;

use Theposeidonas\Kolaybi\Enums\Concerns\HasValues;

enum Currency: string
{
    use HasValues;

    case TRY = 'TRY';
    case USD = 'USD';
    case EUR = 'EUR';
    case GBP = 'GBP';
    case CHF = 'CHF';
    case JPY = 'JPY';
    case CAD = 'CAD';
    case AUD = 'AUD';
    case SEK = 'SEK';
    case NOK = 'NOK';
    case DKK = 'DKK';
    case PLN = 'PLN';
    case CZK = 'CZK';
    case HUF = 'HUF';
    case RUB = 'RUB';
    case CNY = 'CNY';
    case KRW = 'KRW';
    case SAR = 'SAR';
    case AED = 'AED';
    case EGP = 'EGP';
}
