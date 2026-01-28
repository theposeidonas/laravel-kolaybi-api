<?php

namespace Theposeidonas\Kolaybi\Enums;

enum Currency: string
{
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

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
