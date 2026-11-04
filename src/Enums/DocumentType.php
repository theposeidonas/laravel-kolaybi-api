<?php

namespace Theposeidonas\Kolaybi\Enums;

use Theposeidonas\Kolaybi\Enums\Concerns\HasValues;

enum DocumentType: string
{
    use HasValues;

    case SATIS = 'SATIS';
    case ISTISNA = 'ISTISNA';
    case IADE = 'IADE';
    case TEVKIFAT = 'TEVKIFAT';
    case TEVKIFATIADE = 'TEVKIFATIADE';
    case MATBUDAN = 'MATBUDAN';
    case SEVK = 'SEVK';
    case OZELMATRAH = 'OZELMATRAH';
    case IHRACKAYITLI = 'IHRACKAYITLI';
}
