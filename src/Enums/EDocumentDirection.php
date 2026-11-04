<?php

namespace Theposeidonas\Kolaybi\Enums;

use Theposeidonas\Kolaybi\Enums\Concerns\HasValues;

enum EDocumentDirection: string
{
    use HasValues;

    case INBOUND = 'inbound';
    case OUTBOUND = 'outbound';
}
